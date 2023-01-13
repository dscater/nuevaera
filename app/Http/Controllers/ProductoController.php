<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public $validacion = [
        'codigo' => 'required|min:1',
        'nombre' => 'required|min:1',
        'medida' => 'required|min:1',
        'grupo_id' => 'required',
        'precio' => 'required|numeric',
        'precio_mayor' => 'required|numeric',
        'stock_min' => 'required|numeric',
        'descontar_stock' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $productos = Producto::with("grupo")->get();
        return response()->JSON(['productos' => $productos, 'total' => count($productos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Producto
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_producto = Producto::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'producto' => $nueva_producto,
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

    public function update(Request $request, Producto $producto)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $producto->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'producto' => $producto,
                'msj' => 'El registro se actualizó de forma correcta'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'msj' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Producto $producto)
    {
        return response()->JSON([
            'sw' => true,
            'producto' => $producto
        ], 200);
    }

    public function destroy(Producto $producto)
    {
        DB::beginTransaction();
        try {
            $producto->delete();
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
