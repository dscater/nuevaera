<?php

namespace App\Http\Controllers;

use App\Models\IngresoProducto;
use App\Models\KardexProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoProductoController extends Controller
{
    public $validacion = [
        'producto_id' => 'required',
        'proveedor_id' => 'required',
        'precio_compra' => 'required|numeric',
        'cantidad' => 'required|numeric',
        'tipo_ingreso_id' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $ingreso_productos = IngresoProducto::with("producto")->with("proveedor")->with("tipo_ingreso")->get();
        return response()->JSON(['ingreso_productos' => $ingreso_productos, 'total' => count($ingreso_productos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear IngresoProducto
            $request["fecha_registro"] = date("Y-m-d");
            $nuevo_ingreso_producto = IngresoProducto::create(array_map('mb_strtoupper', $request->all()));

            // registrar kardex
            KardexProducto::registroIngreso("ALMACEN", 0, "INGRESO", $nuevo_ingreso_producto->id, $nuevo_ingreso_producto->producto, $nuevo_ingreso_producto->cantidad, $nuevo_ingreso_producto->precio_compra, $nuevo_ingreso_producto->descripcion);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'ingreso_producto' => $nuevo_ingreso_producto,
                'msj' => 'El registro se realizó de forma correcta',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'msj' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, IngresoProducto $ingreso_producto)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // descontar el stock
            $ingreso_producto->producto->almacen->stock_actual =  (float)$ingreso_producto->producto->almacen->stock_actual - (float)$ingreso_producto->cantidad;
            $ingreso_producto->producto->almacen->save();

            $ingreso_producto->update(array_map('mb_strtoupper', $request->all()));

            // INCREMENTAR STOCK
            $ingreso_producto->producto->almacen->stock_actual = (float)$ingreso_producto->producto->almacen->stock_actual + $ingreso_producto->cantidad;
            $ingreso_producto->producto->almacen->save();

            // actualizar kardex
            $kardex = KardexProducto::where("lugar", "ALMACEN")->where("tipo_is", "INGRESO")->where("registro_id", $ingreso_producto->id)->get()->first();
            KardexProducto::actualizaRegistrosKardex($kardex->id, "ALMACEN");

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'ingreso_producto' => $ingreso_producto,
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

    public function show(IngresoProducto $ingreso_producto)
    {
        return response()->JSON([
            'sw' => true,
            'ingreso_producto' => $ingreso_producto
        ], 200);
    }

    public function destroy(IngresoProducto $ingreso_producto)
    {
        DB::beginTransaction();
        try {

            $eliminar_kardex = KardexProducto::where("lugar", "ALMACEN")
                ->where("tipo_registro", "INGRESO")
                ->where("registro_id", $ingreso_producto->id)
                ->get()
                ->first();
            $id_kardex = $eliminar_kardex->id;
            $eliminar_kardex->delete();

            $anterior = KardexProducto::where("lugar", "ALMACEN")
                ->where("id", "<", $id_kardex)
                ->get()
                ->last();
            $actualiza_desde = null;
            if ($anterior) {
                $actualiza_desde = $anterior;
            } else {
                // comprobar si existen registros posteriorres al actualizado
                $siguiente = KardexProducto::where("lugar", "ALMACEN")
                    ->where("id", ">", $id_kardex)
                    ->get()->first();
                if ($siguiente)
                    $actualiza_desde = $siguiente;
            }

            if ($actualiza_desde) {
                // actualizar a partir de este registro los sgtes. registros
                KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, "ALMACEN");
            }

            // descontar el stock
            $ingreso_producto->producto->almacen->stock_actual =  (float)$ingreso_producto->producto->almacen->stock_actual - (float)$ingreso_producto->cantidad;
            $ingreso_producto->producto->almacen->save();
            $ingreso_producto->delete();
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'msj' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'msj' => $e->getMessage(),
            ], 500);
        }
    }
}
