<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PDF;
use App\library\numero_a_letras\src\NumeroALetras;
use App\Models\DetalleOrden;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\HistorialAccion;
use App\Models\KardexProducto;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\SucursalStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenVentaController extends Controller
{
    public $validacion = [
        "caja_id" => "required",
        "cliente_id" => "required",
        "tipo_venta" => "required",
        "descuento" => "required|numeric|min:0|max:100",
        "total_final" => "required",
    ];

    public function index()
    {
        $orden_ventas = OrdenVenta::with("caja")->with("cliente")->get();
        if (Auth::user()->tipo == 'CAJA') {
            $orden_ventas = OrdenVenta::with("caja")->with("cliente")->where("caja_id", Auth::user()->caja_usuario->caja_id)->get();
        }

        return response()->JSON(["orden_ventas" => $orden_ventas, "total" => count($orden_ventas)]);
    }

    public function orden_ventas_caja(Request $request)
    {
        $orden_ventas = OrdenVenta::where("caja_id", $request->id)->get();
        return response()->JSON($orden_ventas);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion);

        DB::beginTransaction();
        try {
            $request["fecha_registro"] = date("Y-m-d");
            $request["estado"] = "CANCELADO";
            $request["user_id"] = Auth::user()->id;
            $orden_venta = OrdenVenta::create(array_map("mb_strtoupper", $request->except("caja", "detalle_ordens", "cliente", "user")));

            $detalle_ordens = $request->detalle_ordens;
            foreach ($detalle_ordens as $value) {
                $dv = $orden_venta->detalle_ordens()->create([
                    "producto_id" => $value["producto_id"],
                    "sucursal_stock_id" => $value["sucursal_stock_id"],
                    "cantidad" => $value["cantidad"],
                    "venta_mayor" => $value["venta_mayor"],
                    "precio" => $value["precio"],
                    "subtotal" => $value["subtotal"],
                ]);
                // registrar kardex
                KardexProducto::registroEgreso("SUCURSAL", "VENTA", $dv->id, $dv->producto, $dv->cantidad, $dv->precio, "VENTA DE PRODUCTO");
            }


            $datos_original = HistorialAccion::getDetalleRegistro($orden_venta, "orden_ventas");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'CREACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' REGISTRO UNA ORDEN DE VENTA',
                'datos_original' => $datos_original,
                'modulo' => 'ORDEN DE VENTA',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);


            if ($orden_venta->tipo_venta == 'A CRÉDITO') {
                $orden_venta->credito()->create(["estado" => "PENDIENTE"]);
                $orden_venta->estado = "PENDIENTE";
                $orden_venta->save();
            }

            DB::commit();
            return response()->JSON(["sw" => true, "orden_venta" => $orden_venta, "id" => $orden_venta->id, "msj" => "El registro se almacenó correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(OrdenVenta $orden_venta)
    {
        return response()->JSON($orden_venta->load("user")->load("caja")->load("cliente")->load("detalle_ordens.producto"));
    }

    public function update(OrdenVenta $orden_venta, Request $request)
    {
        $request->validate($this->validacion);

        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($orden_venta, "orden_ventas");

            $request["estado"] = "CANCELADO";
            $orden_venta->update(array_map("mb_strtoupper", $request->except("caja", "detalle_ordens", "eliminados", "cliente", "user")));
            $detalle_ordens = $request->detalle_ordens;
            foreach ($detalle_ordens as $value) {
                if ($value["id"] == 0) {
                    $dv = $orden_venta->detalle_ordens()->create([
                        "producto_id" => $value["producto_id"],
                        "sucursal_stock_id" => $value["sucursal_stock_id"],
                        "cantidad" => $value["cantidad"],
                        "venta_mayor" => $value["venta_mayor"],
                        "precio" => $value["precio"],
                        "subtotal" => $value["subtotal"],
                    ]);
                    // registrar kardex
                    KardexProducto::registroEgreso("SUCURSAL", "VENTA", $dv->id, $dv->producto, $dv->cantidad, $dv->precio, "VENTA DE PRODUCTO");
                } else {
                    $dv = DetalleOrden::find($value["id"]);

                    if ($dv->producto->descontar_stock == 'SI') {
                        // incrementar el stock
                        Producto::incrementarStock($dv->producto, $dv->cantidad, "SUCURSAL");
                    }

                    // VALIDAR STOCK
                    $sucursal_stock = SucursalStock::where("producto_id", $dv->producto_id)->get()->first();
                    if ($sucursal_stock->stock_actual < $dv->cantidad) {
                        throw new Exception('No es posible realizar el registro debido a que la cantidad supera al stock disponible ' . $sucursal_stock->stock_actual);
                    }

                    $dv->update([
                        "producto_id" => $value["producto_id"],
                        "sucursal_stock_id" => $value["sucursal_stock_id"],
                        "cantidad" => $value["cantidad"],
                        "venta_mayor" => $value["venta_mayor"],
                        "precio" => $value["precio"],
                        "subtotal" => $value["subtotal"],
                    ]);

                    if ($dv->producto->descontar_stock == 'SI') {
                        Producto::decrementarStock($dv->producto, $dv->cantidad, "SUCURSAL");
                    }
                    // actualizar kardex
                    $kardex = KardexProducto::where("lugar", "SUCURSAL")
                        ->where("producto_id", $dv->producto_id)
                        ->where("tipo_registro", "VENTA")
                        ->where("registro_id", $dv->id)
                        ->get()->first();

                    KardexProducto::actualizaRegistrosKardex($kardex->id, $kardex->producto_id, "SUCURSAL");
                }
            }

            $eliminados = $request->eliminados;
            foreach ($eliminados as $value) {
                $dv = DetalleOrden::find($value);
                $producto = Producto::find($dv->producto_id);
                // ACTUALIZAR KARDEX
                $eliminar_kardex = KardexProducto::where("lugar", "SUCURSAL")
                    ->where("tipo_registro", "VENTA")
                    ->where("registro_id", $dv->id)
                    ->where("producto_id", $dv->producto_id)
                    ->get()
                    ->first();
                $id_kardex = $eliminar_kardex->id;
                $id_producto = $eliminar_kardex->producto_id;
                $eliminar_kardex->delete();

                $anterior = KardexProducto::where("lugar", "SUCURSAL")
                    ->where("producto_id", $id_producto)
                    ->where("id", "<", $id_kardex)
                    ->get()
                    ->last();
                $actualiza_desde = null;
                if ($anterior) {
                    $actualiza_desde = $anterior;
                } else {
                    // comprobar si existen registros posteriorres al actualizado
                    $siguiente = KardexProducto::where("lugar", "SUCURSAL")
                        ->where("producto_id", $id_producto)
                        ->where("id", ">", $id_kardex)
                        ->get()->first();
                    if ($siguiente)
                        $actualiza_desde = $siguiente;
                }

                if ($actualiza_desde) {
                    // actualizar a partir de este registro los sgtes. registros
                    KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id, "SUCURSAL");
                }

                // incrementar el stock
                if ($dv->producto->descontar_stock == 'SI') {
                    Producto::incrementarStock($producto, $dv->cantidad, "SUCURSAL");
                }

                $dv->delete();
            }

            $datos_nuevo = HistorialAccion::getDetalleRegistro($orden_venta, "orden_ventas");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' MODIFICÓ UNA ORDEN DE VENTA',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'ORDEN DE VENTA',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            if ($orden_venta->tipo_venta == 'A CRÉDITO') {
                $orden_venta->estado = "PENDIENTE";
                $orden_venta->save();
                if (!$orden_venta->credito) {
                    $orden_venta->credito()->create(["estado" => "PENDIENTE"]);
                } else {
                    $orden_venta->credito->update(["estado" => "PENDIENTE"]);
                }
            }

            DB::commit();
            return response()->JSON(["sw" => true, "orden_venta" => $orden_venta, "id" => $orden_venta->id, "msj" => "El registro se actualizó correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(OrdenVenta $orden_venta)
    {
        DB::beginTransaction();
        try {

            foreach ($orden_venta->detalle_ordens as $dv) {
                $producto = Producto::find($dv->producto_id);
                // ACTUALIZAR KARDEX
                $eliminar_kardex = KardexProducto::where("lugar", "SUCURSAL")
                    ->where("tipo_registro", "VENTA")
                    ->where("registro_id", $dv->id)
                    ->where("producto_id", $dv->producto_id)
                    ->get()
                    ->first();
                $id_kardex = $eliminar_kardex->id;
                $id_producto = $eliminar_kardex->producto_id;
                $eliminar_kardex->delete();

                $anterior = KardexProducto::where("lugar", "SUCURSAL")
                    ->where("producto_id", $id_producto)
                    ->where("id", "<", $id_kardex)
                    ->get()
                    ->last();
                $actualiza_desde = null;
                if ($anterior) {
                    $actualiza_desde = $anterior;
                } else {
                    // comprobar si existen registros posteriorres al actualizado
                    $siguiente = KardexProducto::where("lugar", "SUCURSAL")
                        ->where("producto_id", $id_producto)
                        ->where("id", ">", $id_kardex)
                        ->get()->first();
                    if ($siguiente)
                        $actualiza_desde = $siguiente;
                }

                if ($actualiza_desde) {
                    // actualizar a partir de este registro los sgtes. registros
                    KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id, "SUCURSAL");
                }

                // incrementar el stock
                if ($dv->producto->descontar_stock == 'SI') {
                    Producto::incrementarStock($producto, $dv->cantidad, "SUCURSAL");
                }

                $dv->delete();
            }
            if ($orden_venta->credito) {
                $orden_venta->credito->delete();
            }

            $datos_original = HistorialAccion::getDetalleRegistro($orden_venta, "orden_ventas");
            $orden_venta->delete();
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' ELIMINÓ UNA ORDEN DE VENTA',
                'datos_original' => $datos_original,
                'modulo' => 'ORDEN DE VENTA',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            DB::commit();
            return response()->JSON(["sw" => true, "msj" => "El registro se eliminó correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLiteral(Request $request)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', number_format($request->total, 2, '.', ''));
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
        return response()->JSON($literal);
    }

    public function getDevolucions(Request $request)
    {
        $orden_venta = OrdenVenta::findOrFail($request->id);
        $devolucion = Devolucion::with("devolucion_detalles.producto")->with("devolucion_detalles.detalle_orden")->where("orden_id", $request->id)->get()->first();
        $total_cantidad_devolucion = 0;
        $total_final = 0;
        $p_descuento = $orden_venta->descuento / 100;
        $descuento = 0;
        if ($devolucion) {
            $orden_venta = $devolucion->orden;
            $total_final = $devolucion->orden->total;
            $total_cantidad_devolucion = DevolucionDetalle::where("devolucion_id", $devolucion->id)->sum("cantidad");
            if ($total_cantidad_devolucion > 0) {
                $total_devolucion = 0;
                foreach ($orden_venta->detalle_ordens as $do) {
                    // restar totales
                    $detalle_devolucion = DevolucionDetalle::where("detalle_orden_id", $do->id)->get()->first();
                    if ($detalle_devolucion && $detalle_devolucion->cantidad > 0) {
                        $total_devolucion += (float)$detalle_devolucion->cantidad * $do->precio;
                    }
                }
                $total_final = (float)$total_final - $total_devolucion;
            }
            $descuento = $total_final * $p_descuento;
            $total_final = $total_final - $descuento;
        } else {
            $total_final = $orden_venta->total_final;
        }

        return response()->JSON([
            "devolucion" => $devolucion,
            "total_cantidad_devolucion" => $total_cantidad_devolucion,
            "p_descuento" => $p_descuento,
            "total_final" => number_format($total_final, 2, '.', '')
        ]);
    }

    public function pdf(OrdenVenta $orden_venta)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $orden_venta->total);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";

        $nro_factura = (int)$orden_venta->id;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }

        $customPaper = array(0, 0, 360, 600);

        $pdf = PDF::loadView('reportes.orden_venta', compact('orden_venta', 'literal', 'nro_factura'))->setPaper('legal', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->download('Usuarios.pdf');
    }
}
