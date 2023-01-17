<?php

namespace App\Http\Controllers;

use App\Models\TipoIngreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoIngresoController extends Controller
{
    public $validacion = [
        'nombre' => 'required|min:2',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $tipo_ingresos = TipoIngreso::all();
        return response()->JSON(['tipo_ingresos' => $tipo_ingresos, 'total' => count($tipo_ingresos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear TipoIngreso
            $nueva_tipo_ingreso = TipoIngreso::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'tipo_ingreso' => $nueva_tipo_ingreso,
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

    public function update(Request $request, TipoIngreso $tipo_ingreso)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $tipo_ingreso->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'tipo_ingreso' => $tipo_ingreso,
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

    public function show(TipoIngreso $tipo_ingreso)
    {
        return response()->JSON([
            'sw' => true,
            'tipo_ingreso' => $tipo_ingreso
        ], 200);
    }

    public function destroy(TipoIngreso $tipo_ingreso)
    {
        DB::beginTransaction();
        try {
            $tipo_ingreso->delete();
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
