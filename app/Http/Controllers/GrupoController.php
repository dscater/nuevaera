<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public $validacion = [
        'nombre' => 'required|min:2',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $grupos = Grupo::all();
        return response()->JSON(['grupos' => $grupos, 'total' => count($grupos)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Grupo
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_grupo = Grupo::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'grupo' => $nueva_grupo,
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

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $grupo->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'grupo' => $grupo,
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

    public function show(Grupo $grupo)
    {
        return response()->JSON([
            'sw' => true,
            'grupo' => $grupo
        ], 200);
    }

    public function destroy(Grupo $grupo)
    {
        DB::beginTransaction();
        try {
            $grupo->delete();
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
