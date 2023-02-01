<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\HistorialAccion;
use App\Models\OrdenVenta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    public $validacion = [
        'codigo' => 'required|min:1',
        'nombre' => 'required|min:2',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $cajas = Caja::all();
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

            $datos_original = HistorialAccion::getDetalleRegistro($nueva_caja, "cajas");
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

            $datos_original = HistorialAccion::getDetalleRegistro($caja, "cajas");
            $caja->update(array_map('mb_strtoupper', $request->all()));

            $datos_nuevo = HistorialAccion::getDetalleRegistro($caja, "cajas");
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
            // validar que no exista en orden de ventas
            $orden_ventas = OrdenVenta::where("caja_id", $caja->id)->get();
            if (count($orden_ventas) > 0) {
                throw new Exception('No es posible eliminar el registro debido a que se realizaron Orden de ventas en esta caja');
            }

            $datos_original = HistorialAccion::getDetalleRegistro($caja, "cajas");
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
