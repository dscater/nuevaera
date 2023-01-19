<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Models\SalidaProducto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaProductoController extends Controller
{
    public $validacion = [
        'producto_id' => 'required',
        'cantidad' => 'required|numeric',
        'tipo_salida_id' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $salida_productos = SalidaProducto::with("producto")->with("tipo_salida")->get();
        return response()->JSON(['salida_productos' => $salida_productos, 'total' => count($salida_productos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);


        DB::beginTransaction();
        try {
            $producto = Producto::find($request->producto_id);
            if ($producto->descontar_stock == 'SI') {
                // VALIDAR STOCK
                $stock_producto = Almacen::where("producto_id", $request->producto_id)->get()->first();
                if ($stock_producto->stock_actual < $request->cantidad) {
                    throw new Exception('No es posible realizar el registro debido a que la cantidad supera al stock actual ' . $stock_producto->stock_actual);
                }
            }
            // crear SalidaProducto
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_salida_producto = SalidaProducto::create(array_map('mb_strtoupper', $request->all()));

            // registrar kardex
            KardexProducto::registroEgreso("ALMACEN", 0, "SALIDA", $nueva_salida_producto->id, $nueva_salida_producto->producto, $nueva_salida_producto->cantidad, $nueva_salida_producto->producto->precio, $nueva_salida_producto->descripcion);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'salida_producto' => $nueva_salida_producto,
                'msj' => 'El registro se realizó de forma correcta',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, SalidaProducto $salida_producto)
    {
        $request->validate($this->validacion, $this->mensajes);

        $request->validate($this->validacion, $this->mensajes);
        if ($request->producto_id != $salida_producto->producto_id) {
            return response()->JSON([
                'sw' => false,
                'message' => "Error, los datos enviados son incorrectos",
            ], 500);
        } else {
            DB::beginTransaction();
            try {
                if ($salida_producto->producto->descontar_stock == 'SI') {
                    // incrementar el stock
                    Producto::incrementarStock($salida_producto->producto, $salida_producto->cantidad, "ALMACEN");

                    // VALIDAR STOCK
                    $stock_producto = Almacen::where("producto_id", $salida_producto->producto_id)->get()->first();
                    if ($stock_producto->stock_actual < $request->cantidad) {
                        throw new Exception('No es posible realizar el registro debido a que la cantidad supera al stock disponible ' . $stock_producto->stock_actual);
                    }
                }

                $salida_producto->update(array_map('mb_strtoupper', $request->all()));

                if ($salida_producto->producto->descontar_stock == 'SI') {
                    // DESCONTAR STOCK
                    Producto::decrementarStock($salida_producto->producto, $salida_producto->cantidad, "ALMACEN");
                }

                // actualizar kardex
                $kardex = KardexProducto::where("lugar", "ALMACEN")
                    ->where("producto_id", $salida_producto->producto_id)
                    ->where("tipo_registro", "SALIDA")
                    ->where("registro_id", $salida_producto->id)
                    ->get()->first();
                KardexProducto::actualizaRegistrosKardex($kardex->id, $kardex->producto_id, "ALMACEN");

                DB::commit();
                return response()->JSON([
                    'sw' => true,
                    'salida_producto' => $salida_producto,
                    'msj' => 'El registro se actualizó de forma correcta'
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->JSON([
                    'sw' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }
    }

    public function show(SalidaProducto $salida_producto)
    {
        return response()->JSON([
            'sw' => true,
            'salida_producto' => $salida_producto
        ], 200);
    }

    public function destroy(SalidaProducto $salida_producto)
    {
        DB::beginTransaction();
        try {
            $eliminar_kardex = KardexProducto::where("lugar", "ALMACEN")
                ->where("tipo_registro", "SALIDA")
                ->where("registro_id", $salida_producto->id)
                ->where("producto_id", $salida_producto->producto_id)
                ->get()
                ->first();
            $id_kardex = $eliminar_kardex->id;
            $id_producto = $eliminar_kardex->producto_id;
            $eliminar_kardex->delete();

            $anterior = KardexProducto::where("lugar", "ALMACEN")
                ->where("producto_id", $id_producto)
                ->where("id", "<", $id_kardex)
                ->get()
                ->last();
            $actualiza_desde = null;
            if ($anterior) {
                $actualiza_desde = $anterior;
            } else {
                // comprobar si existen registros posteriorres al actualizado
                $siguiente = KardexProducto::where("lugar", "ALMACEN")
                    ->where("producto_id", $id_producto)
                    ->where("id", ">", $id_kardex)
                    ->get()->first();
                if ($siguiente)
                    $actualiza_desde = $siguiente;
            }

            if ($actualiza_desde) {
                // actualizar a partir de este registro los sgtes. registros
                KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id, "ALMACEN");
            }

            // incrementar el stock
            if ($salida_producto->producto->descontar_stock == 'SI') {
                Producto::incrementarStock($salida_producto->producto, $salida_producto->cantidad, "ALMACEN");
            }

            $salida_producto->delete();
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'msj' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
