<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\ImportancionApertura;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalController extends Controller
{
    // "nombre" => "required|regex:/^[\pL\s\-]+$/u|min:2",

    public $validacion = [
        'codigo' => 'required|min:4',
        'nombre' => 'required|min:4',
        'dir' => 'required|min:4',
        'fono' => 'required|min:4',
        'responsable' => 'required|min:4',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $sucursals = Sucursal::all();

        return response()->JSON(['sucursals' => $sucursals, 'total' => count($sucursals)], 200);
    }


    public function sin_importacion(Request $request)
    {
        $sucursals = Sucursal::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('importancion_aperturas')
                ->whereRaw('sucursals.id = importancion_aperturas.registro_id');
        })->get();
        // verificiar almacen
        $existe_importacion_almacen = ImportancionApertura::where("lugar", "ALMACEN")->get()->first();
        if ($existe_importacion_almacen) {
            $almacen = true;
        } else {
            $almacen = false;
        }
        return response()->JSON(['sucursals' => $sucursals, 'almacen' => $almacen, 'total' => count($sucursals)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Sucursal
            $request["fecha_registro"] = date("Y-m-d");
            $nueva_sucursal = Sucursal::create(array_map('mb_strtoupper', $request->all()));

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'sucursal' => $nueva_sucursal,
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

    public function update(Request $request, Sucursal $sucursal)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            $sucursal->update(array_map('mb_strtoupper', $request->all()));
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'sucursal' => $sucursal,
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

    public function show(Sucursal $sucursal)
    {
        return response()->JSON([
            'sw' => true,
            'sucursal' => $sucursal
        ], 200);
    }

    public function destroy(Sucursal $sucursal)
    {
        DB::beginTransaction();
        try {
            $sucursal->delete();
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

    public function getCajas(Request $request)
    {
        $cajas = Caja::where("sucursal_id", $request->id)->get();
        return response()->JSON($cajas);
    }
}
