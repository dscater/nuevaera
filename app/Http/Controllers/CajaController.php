<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\HistorialAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            $datos_original =  implode("|", $nueva_caja->attributesToArray());
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'CREACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' REGISTRO UNA NUEVA CAJA',
                'datos_original' => $datos_original,
                'modulo' => 'CAJAS',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

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
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Caja $caja)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {

            $datos_original =  implode("|", $caja->attributesToArray());

            $caja->update(array_map('mb_strtoupper', $request->all()));

            $datos_nuevo =  implode("|", $caja->attributesToArray());
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' MODIFICÓ UNA CAJA',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'CAJAS',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

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
                'message' => $e->getMessage(),
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
            $datos_original =  implode("|", $caja->attributesToArray());
            $caja->delete();
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' ELIMINÓ UNA CAJA',
                'datos_original' => $datos_original,
                'modulo' => 'CAJAS',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

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

    public function cajas_sucursal(Request $request)
    {
        return response()->JSON(Caja::where("sucursal_id", $request->id)->get());
    }
}
