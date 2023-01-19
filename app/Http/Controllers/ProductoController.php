<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\SucursalStock;
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

    public function buscar_producto(Request $request)
    {
        $value = $request->value;
        $productos = Producto::select("productos.*")->with("grupo")
            ->where("grupo")
            ->get();
        return response()->JSON($productos);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear Producto
            $request["fecha_registro"] = date("Y-m-d");
            $nuevo_producto = Producto::create(array_map('mb_strtoupper', $request->all()));

            // registrar en almacen
            $nuevo_producto->almacen()->create([
                "stock_actual" => 0,
            ]);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'producto' => $nuevo_producto,
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
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Producto $producto, Request $request)
    {
        if (isset($request->id)) {
            $stock_sucursal = SucursalStock::where("producto_id", $producto->id)->where("sucursal_id", $request->id)->get()->first();
        } else {
            $stock_sucursal["stock_actual"] = 0;
        }

        return response()->JSON([
            'sw' => true,
            'producto' => $producto->load("grupo"),
            "stock_actual" => $stock_sucursal["stock_actual"]
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
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStock(Request $request)
    {
        $lugar = $request->lugar;
        $lugar_id = $request->lugar_id;
        $producto_id = $request->producto_id;

        $producto = Producto::find($producto_id);
        if ($lugar == 'SUCURSAL') {
            $stock = SucursalStock::where("sucursal_id", $lugar_id)->where("producto_id", $producto_id)->get()->first();
            if (!$stock) {
                $stock = SucursalStock::create([
                    "sucursal_id" => $lugar_id,
                    "producto_id" => $producto_id,
                    "stock_actual" => 0,
                ]);
            }
        } else {
            $stock = Almacen::where("producto_id", $producto_id)->get()->first();
            if (!$stock) {
                $stock = Almacen::create([
                    "producto_id" => $producto_id,
                    "stock_actual" => 0,
                ]);
            }
        }

        return response()->JSON([
            "producto" => $producto,
            "stock_actual" => $stock->stock_actual
        ]);
    }

    public function productos_sucursal(Request $request)
    {
        $productos = SucursalStock::with("sucursal")->with("producto.grupo")->where("sucursal_id", $request->id)->get();
        return response()->JSON($productos);
    }

    public function valida_stock(Request $request)
    {
        $cantidad = $request->cantidad;
        $sucursal_stock = SucursalStock::where("sucursal_id", $request->sucursal_id)->where("producto_id", $request->id)->get()->first();

        if ($sucursal_stock->stock_actual >= $cantidad) {
            return response()->JSON(
                [
                    "sw" => true,
                    "producto" => $sucursal_stock->producto,
                    "sucursal_stock" => $sucursal_stock,
                ]
            );
        }
        return response()->JSON(
            [
                "sw" => false,
                "msj" => "La cantidad que desea ingresar supera al stock disponible del producto.<br/> Stock actual: <b>" . $sucursal_stock->stock_actual . " unidades</b>"
            ]
        );
    }
}
