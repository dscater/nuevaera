<?php

namespace App\Http\Controllers;

use App\Models\TipoSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoSalidaController extends Controller
{
    public $validacion = [
        'nombre' => 'required|min:2',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $tipo_salidas = TipoSalida::all();
        return response()->JSON(['tipo_salidas' => $tipo_salidas, 'total' => count($tipo_salidas)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear TipoSalida
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_tipo_salida = TipoSalida::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'tipo_salida' => $nueva_tipo_salida,
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

    public function update(Request $request, TipoSalida $tipo_salida)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $tipo_salida->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'tipo_salida' => $tipo_salida,
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

    public function show(TipoSalida $tipo_salida)
    {
        return response()->JSON([
            'sw' => true,
            'tipo_salida' => $tipo_salida
        ], 200);
    }

    public function destroy(TipoSalida $tipo_salida)
    {
        DB::beginTransaction();
        try {
            $tipo_salida->delete();
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
