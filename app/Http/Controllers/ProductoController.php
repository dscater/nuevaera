<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\DetalleOrden;
use App\Models\HistorialAccion;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Models\SucursalStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $productos = Producto::with("grupo")->orderBy("nombre", "asc")->get();
        return response()->JSON(['productos' => $productos, 'total' => count($productos)], 200);
    }

    public function paginado(Request $request)
    {
        $productos = [];
        $sortBy = $request->sortBy;
        $sortDesc = $request->sortDesc;

        if (isset($request->importacion)) {
            $lugar = $request->lugar;
            if ($lugar == 'ALMACEN') {
                $productos = Producto::select("productos.*")->with("grupo")->join("grupos", "grupos.id", "=", "productos.grupo_id")
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('almacens')
                            ->whereRaw('productos.id = almacens.producto_id');
                    })->paginate();
            } else {
                $productos = Producto::select("productos.*")->with("grupo")->join("grupos", "grupos.id", "=", "productos.grupo_id")
                    ->whereNotExists(function ($query) use ($lugar) {
                        $query->select(DB::raw(1))
                            ->from('sucursal_stocks')
                            ->whereRaw('productos.id = sucursal_stocks.producto_id')
                            ->whereRaw('sucursal_stocks.sucursal_id = ' . $lugar);
                    })->paginate();
            }
        } else {
            if (isset($request->value) && $request->value != "") {
                $value = $request->value;
                $productos = Producto::select("productos.*")->with("grupo")
                    ->join("grupos", "grupos.id", "=", "productos.grupo_id")
                    ->orWhere("productos.id", "LIKE", "%$value%")
                    ->orWhere("productos.codigo", "LIKE", "%$value%")
                    ->orWhere("productos.nombre", "LIKE", "%$value%")
                    ->orWhere("productos.medida", "LIKE", "%$value%")
                    ->orWhere("productos.precio", "LIKE", "%$value%")
                    ->orWhere("productos.precio_mayor", "LIKE", "%$value%")
                    ->orWhere("productos.fecha_registro", "LIKE", "%$value%")
                    ->orWhere("grupos.nombre", "LIKE", "%$value%");
            } else {
                $productos = Producto::select("productos.*")->with("grupo")->join("grupos", "grupos.id", "=", "productos.grupo_id");
            }
            if (isset($sortBy) && $sortBy != "") {
                $asc_desc = "ASC";
                if ($sortDesc == "true") {
                    $asc_desc = "DESC";
                }
                if ($sortBy != "grupo.nombre") {
                    $productos = $productos->orderBy("productos." . $sortBy, $asc_desc);
                } else {
                    $productos = $productos->orderBy("grupos.nombre", $asc_desc);
                }
            }
            $productos = $productos->paginate($request->per_page);
        }

        return response()->JSON(['productos' => $productos, 'total' => count($productos)], 200);
    }

    public function buscar_producto(Request $request)
    {
        $value = $request->value;
        // $productos = Producto::select("productos.*")
        //     ->join("grupos", "grupos.id", "=", "productos.grupo_id")
        //     ->where(DB::raw('CONCAT(productos.id, productos.codigo, productos.nombre, productos.medida, productos.precio, productos.precio_mayor, productos.fecha_registro, grupos.nombre)'), 'LIKE', "%$value%")
        //     ->get();
        $productos = Producto::select("productos.*")
            ->join("grupos", "grupos.id", "=", "productos.grupo_id")
            ->orWhere("productos.id", "LIKE", "%$value%")
            ->orWhere("productos.codigo", "LIKE", "%$value%")
            ->orWhere("productos.nombre", "LIKE", "%$value%")
            ->orWhere("productos.medida", "LIKE", "%$value%")
            ->orWhere("productos.precio", "LIKE", "%$value%")
            ->orWhere("productos.precio_mayor", "LIKE", "%$value%")
            ->orWhere("productos.fecha_registro", "LIKE", "%$value%")
            ->orWhere("grupos.nombre", "LIKE", "%$value%")
            ->get()->take(100);
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

            $datos_original =  implode("|", $nuevo_producto->attributesToArray());
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'CREACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' REGISTRO UN PRODUCTO',
                'datos_original' => $datos_original,
                'modulo' => 'PRODUCTOS',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
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
            $datos_original =  implode("|", $producto->attributesToArray());
            $producto->update(array_map('mb_strtoupper', $request->all()));
            $datos_nuevo =  implode("|", $producto->attributesToArray());
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' MODIFICÓ UN PRODUCTO',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'PRODUCTOS',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

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
        $stock_sucursal["stock_actual"] = 0;
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
            // validar que no exista en orden de ventas
            $orden_ventas = DetalleOrden::where("producto_id", $producto->id)->get();
            if (count($orden_ventas) > 0) {
                throw new Exception('No es posible eliminar el registro debido a que se realizaron Orden de ventas con este producto');
            }

            if ($producto->almacen) {
                $elimina_kardex = KardexProducto::where("lugar", "ALMACEN")
                    ->where("producto_id", $producto->id)
                    ->get()->first();
                if ($elimina_kardex) {
                    $elimina_kardex->delete();
                }
                $producto->almacen->delete();
            }

            if (count($producto->stock_sucursal) > 0) {
                foreach ($producto->stock_sucursal as $value) {
                    $elimina_kardex = KardexProducto::where("lugar", "SUCURSAL")
                        ->where("lugar_id", $value->sucursal_id)
                        ->where("producto_id", $producto->id)
                        ->get()->first();
                    if ($elimina_kardex) {
                        $elimina_kardex->delete();
                    }
                    $value->delete();
                }
            }

            $datos_original =  implode("|", $producto->attributesToArray());
            $producto->delete();
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' ELIMINO UN PRODUCTO',
                'datos_original' => $datos_original,
                'modulo' => 'PRODUCTOS',
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
        $productos = [];
        if (isset($request->value)) {
            $value = $request->value;
            $productos = SucursalStock::select("sucursal_stocks.*")
                ->with("sucursal")->with("producto.grupo")
                ->join("productos", "productos.id", "=", "sucursal_stocks.producto_id")
                ->join("grupos", "grupos.id", "=", "productos.grupo_id")
                ->where("sucursal_stocks.sucursal_id", $request->id)
                ->where(function ($query) use ($value) {
                    $query->where('productos.id', "%$value%")
                        ->orWhere('productos.id', "LIKE", "%$value%")
                        ->orWhere("productos.codigo", "LIKE", "%$value%")
                        ->orWhere("productos.nombre", "LIKE", "%$value%")
                        ->orWhere("productos.medida", "LIKE", "%$value%")
                        ->orWhere("productos.precio", "LIKE", "%$value%")
                        ->orWhere("productos.precio_mayor", "LIKE", "%$value%")
                        ->orWhere("productos.fecha_registro", "LIKE", "%$value%")
                        ->orWhere("grupos.nombre", "LIKE", "%$value%");
                })
                ->get()->take(100);
        } else {
            $productos = SucursalStock::with("sucursal")->with("producto.grupo")->where("sucursal_id", $request->id)->get();
        }
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
