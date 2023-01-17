<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public $validacion = [
        'nombre' => 'required',
        'fono' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $proveedors = Proveedor::all();
        return response()->JSON(['proveedors' => $proveedors, 'total' => count($proveedors)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Proveedor
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_proveedor = Proveedor::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'proveedor' => $nueva_proveedor,
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

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $proveedor->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'proveedor' => $proveedor,
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

    public function show(Proveedor $proveedor)
    {
        return response()->JSON([
            'sw' => true,
            'proveedor' => $proveedor
        ], 200);
    }

    public function destroy(Proveedor $proveedor)
    {
        DB::beginTransaction();
        try {
            $proveedor->delete();
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
