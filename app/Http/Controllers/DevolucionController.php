<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrden;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Models\SucursalStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionController extends Controller
{
    public $validacion = [
        "orden_id" => "required",
    ];

    public function index()
    {
        $devolucions = Devolucion::with("orden.cliente")->get();
        if (Auth::user()->tipo == 'CAJA') {
            $devolucions = Devolucion::select("devolucions.*")
                ->with("orden.cliente")
                ->join("orden_ventas", "orden_ventas.id", "=", "devolucions.orden_id")
                ->where("orden_ventas.sucursal_id", Auth::user()->sucursal->sucursal_id)
                ->get();
        }

        return response()->JSON(["devolucions" => $devolucions, "total" => count($devolucions)]);
    }

    public function devolucions_sucursal(Request $request)
    {
        $devolucions = Devolucion::with("sucursal")->where("sucursal_id", $request->id)->get();
        return response()->JSON($devolucions);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion);

        DB::beginTransaction();
        try {
            $request["fecha_registro"] = date("Y-m-d");
            $request["user_id"] = Auth::user()->id;
            $devolucion = Devolucion::create(array_map("mb_strtoupper", $request->except("devolucion_detalles")));

            $devolucion_detalles = $request->devolucion_detalles;
            foreach ($devolucion_detalles as $value) {
                $dv = $devolucion->devolucion_detalles()->create([
                    "detalle_orden_id" => $value["detalle_orden_id"],
                    "producto_id" => $value["producto_id"],
                    "sucursal_stock_id" => $value["sucursal_stock_id"],
                    "cantidad" => $value["cantidad"],
                ]);
                if ($value["cantidad"] > 0) {
                    // registrar kardex
                    KardexProducto::registroIngreso("SUCURSAL", $devolucion->orden->sucursal_id, "DEVOLUCION", $dv->id, $dv->producto, $dv->cantidad, $dv->detalle_orden->precio, "DEVOLUCIÓN DE PRODUCTO");
                }
            }
            DB::commit();
            return response()->JSON(["sw" => true, "devolucion" => $devolucion, "id" => $devolucion->id, "msj" => "El registro se almacenó correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Devolucion $devolucion)
    {
        return response()->JSON($devolucion->load("orden.detalle_ordens")->load("devolucion_detalles.producto"));
    }

    public function update(Devolucion $devolucion, Request $request)
    {
        $request->validate($this->validacion);

        DB::beginTransaction();
        try {
            $devolucion->update(array_map("mb_strtoupper", $request->except("sucursal", "orden", "devolucion_detalles", "eliminados")));
            $devolucion_detalles = $request->devolucion_detalles;
            foreach ($devolucion_detalles as $value) {
                if ($value["id"] == 0) {
                    $dv = $devolucion->devolucion_detalles()->create([
                        "detalle_orden_id" => $value["detalle_orden_id"],
                        "producto_id" => $value["producto_id"],
                        "sucursal_stock_id" => $value["sucursal_stock_id"],
                        "cantidad" => $value["cantidad"],
                    ]);
                    if ($value["cantidad"] > 0) {
                        // registrar kardex
                        KardexProducto::registroIngreso("SUCURSAL", $devolucion->orden->sucursal_id, "DEVOLUCION", $dv->id, $dv->producto, $dv->cantidad, $dv->detalle_orden->precio, "DEVOLUCIÓN DE PRODUCTO");
                    }
                } else {
                    $dv = DevolucionDetalle::find($value["id"]);
                    // SI LA CANTIDAD CAMBIO ACTUALIZAR
                    if ((float)$value["cantidad"] <= 0) {
                        // eliminar
                        // igual que actualizar Orden de venta
                        $d_orden = $dv->detalle_orden;
                        if ($d_orden->producto->descontar_stock == 'SI') {
                            // incrementar el stock
                            Producto::decrementarStock($dv->detalle_orden->producto, $dv->cantidad, "SUCURSAL", $dv->detalle_orden->orden->sucursal_id);
                        }
                        $dv->update([
                            "cantidad" => $value["cantidad"]
                        ]);
                        $eliminar_kardex = KardexProducto::where("lugar", "SUCURSAL")
                            ->where("lugar_id", $d_orden->orden->sucursal_id)
                            ->where("tipo_registro", "DEVOLUCION")
                            ->where("registro_id", $dv->id)
                            ->where("producto_id", $d_orden->producto_id)
                            ->get()
                            ->first();
                        if ($eliminar_kardex) {
                            $id_kardex = $eliminar_kardex->id;
                            $id_producto = $eliminar_kardex->producto_id;
                            $eliminar_kardex->delete();
                            $anterior = KardexProducto::where("lugar", "SUCURSAL")
                                ->where("lugar_id", $d_orden->orden->sucursal_id)
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
                                    ->where("lugar_id", $d_orden->orden->sucursal_id)
                                    ->where("producto_id", $id_producto)
                                    ->where("id", ">", $id_kardex)
                                    ->get()->first();
                                if ($siguiente)
                                    $actualiza_desde = $siguiente;
                            }
                            if ($actualiza_desde) {
                                // actualizar a partir de este registro los sgtes. registros
                                KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id, "SUCURSAL", $d_orden->orden->sucursal_id);
                            }
                        }
                    } else {
                        // actualizar
                        // igual que actualizar Orden de venta
                        $d_orden = $dv->detalle_orden;
                        if ($d_orden->producto->descontar_stock == 'SI') {
                            // incrementar el stock
                            Producto::decrementarStock($dv->producto, $dv->cantidad, "SUCURSAL", $dv->detalle_orden->orden->sucursal_id);
                        }

                        $dv->update([
                            "cantidad" => $value["cantidad"]
                        ]);

                        $kardex_devolucion = KardexProducto::where("lugar", "SUCURSAL")
                            ->where("lugar_id", $d_orden->orden->sucursal_id)
                            ->where("producto_id", $d_orden->producto_id)
                            ->where("tipo_registro", "DEVOLUCION")
                            ->where("registro_id", $dv->id)
                            ->get()->first();
                        if (!$kardex_devolucion) {
                            // registrar kardex
                            KardexProducto::registroIngreso("SUCURSAL", $devolucion->orden->sucursal_id, "DEVOLUCION", $dv->id, $dv->producto, $dv->cantidad, $dv->detalle_orden->precio, "DEVOLUCIÓN DE PRODUCTO");
                        } else {
                            if ($d_orden->producto->descontar_stock == 'SI') {
                                Producto::incrementarStock($d_orden->producto, $value["cantidad"], "SUCURSAL", $d_orden->orden->sucursal_id);
                            }
                            KardexProducto::actualizaRegistrosKardex($kardex_devolucion->id, $kardex_devolucion->producto_id, "SUCURSAL", $d_orden->orden->sucursal_id);
                        }
                    }
                }
            }
            DB::commit();
            return response()->JSON(["sw" => true, "devolucion" => $devolucion, "id" => $devolucion->id, "msj" => "El registro se actualizó correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Devolucion $devolucion)
    {
        DB::beginTransaction();
        try {

            foreach ($devolucion->devolucion_detalles as $dv) {
                // eliminar
                $d_orden = $dv->detalle_orden;
                if ($dv->producto->descontar_stock == 'SI') {
                    Producto::decrementarStock($dv->producto, $dv->cantidad, "SUCURSAL", $dv->detalle_orden->orden->sucursal_id);
                }
                // actualizar kardex
                $eliminar_kardex = KardexProducto::where("lugar", "SUCURSAL")
                    ->where("lugar_id", $d_orden->orden->sucursal_id)
                    ->where("tipo_registro", "DEVOLUCION")
                    ->where("registro_id", $dv->id)
                    ->where("producto_id", $d_orden->producto_id)
                    ->get()
                    ->first();
                if ($eliminar_kardex) {
                    $id_kardex = $eliminar_kardex->id;
                    $id_producto = $eliminar_kardex->producto_id;
                    $eliminar_kardex->delete();
                    $anterior = KardexProducto::where("lugar", "SUCURSAL")
                        ->where("lugar_id", $d_orden->orden->sucursal_id)
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
                            ->where("lugar_id", $d_orden->orden->sucursal_id)
                            ->where("producto_id", $id_producto)
                            ->where("id", ">", $id_kardex)
                            ->get()->first();
                        if ($siguiente)
                            $actualiza_desde = $siguiente;
                    }
                    if ($actualiza_desde) {
                        // actualizar a partir de este registro los sgtes. registros
                        KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id, "SUCURSAL", $d_orden->orden->sucursal_id);
                    }
                }
                $dv->delete();
            }
            $devolucion->delete();
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
}
