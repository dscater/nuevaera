<?php

namespace App\Http\Controllers;

use App\Models\KardexProducto;
use App\Models\SalidaProducto;
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
        $salida_productos = SalidaProducto::all();
        return response()->JSON(['salida_productos' => $salida_productos, 'total' => count($salida_productos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
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
                'msj' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, SalidaProducto $salida_producto)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $salida_producto->update(array_map('mb_strtoupper', $request->all()));
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
