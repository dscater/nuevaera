<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public $validacion = [
        'codigo' => 'required|min:1',
        'nombre' => 'required|min:2',
        'sucursal_id' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $cajas = Caja::with("sucursal")->get();
        return response()->JSON(['cajas' => $cajas, 'total' => count($cajas)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Caja
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_caja = Caja::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'caja' => $nueva_caja,
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

    public function update(Request $request, Caja $caja)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $caja->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'caja' => $caja,
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

    public function show(Caja $caja)
    {
        return response()->JSON([
            'sw' => true,
            'caja' => $caja
        ], 200);
    }

    public function destroy(Caja $caja)
    {
        DB::beginTransaction();
        try {
            $caja->delete();
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
