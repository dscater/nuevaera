<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Grupo;
use App\Models\HistorialAccion;
use App\Models\ImportancionApertura;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Models\SucursalStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportancionAperturaController extends Controller
{
    public $validacion = [
        'lugar' => 'required',
    ];

    public $mensajes = [];

    public function index(Request $request)
    {
        $importacion_aperturas = ImportancionApertura::all();
        return response()->JSON(['importacion_aperturas' => $importacion_aperturas, 'total' => count($importacion_aperturas)], 200);
    }

    public function store(Request $request)
    {
        $request->validate($this->validacion, $this->mensajes);

        DB::beginTransaction();
        try {
            // crear ImportancionApertura
            $datos = [];
            if ($request->lugar == 'ALMACEN') {
                $datos = [
                    "lugar" => "ALMACEN",
                    "total_registros" => 0,
                    "cambio_stock" => 1,
                ];
            } else {
                $datos = [
                    "lugar" => "SUCURSAL",
                    "total_registros" => 0,
                    "cambio_stock" => 1,
                ];
            }

            $nueva_importacion_apertura = ImportancionApertura::create($datos);

            $datos_original = HistorialAccion::getDetalleRegistro($nueva_importacion_apertura, "importancion_aperturas");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'IMPORTACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . ' REALIZÓ UNA IMPORTACIÓN DE APERTURA',
                'datos_original' => $datos_original,
                'modulo' => 'IMPORTACIÓN DE APERTURA',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'importacion_apertura' => $nueva_importacion_apertura,
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
    public function show(ImportancionApertura $importacion_apertura)
    {
        return response()->JSON([
            'sw' => true,
            'importacion_apertura' => $importacion_apertura
        ], 200);
    }


    public function update(ImportancionApertura $importacion_apertura)
    {
        $importacion_apertura->update(["cambio_stock" => 0]);
        return response()->JSON([
            'sw' => true,
            'importacion_apertura' => $importacion_apertura,
            'msj' => 'Importación finalizada con éxito',
        ], 200);
    }

    public function verifica_importacion(Request $request)
    {
        $existe = ImportancionApertura::where("lugar", $request->lugar)->get()->first();
        if (!$existe) {
            $existe = ImportancionApertura::create([
                "lugar" => $request->lugar,
                "total_registros" => count(Producto::all()),
                "cambio_stock" => 1,
            ]);
        }
        return response()->JSON([
            "sw" => true,
            "importacion_apertura" => $existe
        ]);
    }

    public function actualiza_stock(Request $request)
    {
        DB::beginTransaction();
        try {
            // crear ImportancionApertura
            $col = $request->col;
            $cantidad = $request->cantidad;
            $precio = $request->precio;
            $stock_min = $request->stock_min;

            $producto = Producto::findOrFail($request->id);

            if ($col == 'cantidad') {
                if ($request->lugar == 'ALMACEN') {
                    $existe = Almacen::where("producto_id", $producto->id)->get()->first();
                    if (!$existe) {
                        $existe = Almacen::create([
                            "producto_id" => $producto->id,
                            "stock_actual" => $cantidad,
                        ]);
                        $monto = (float)$cantidad * (float)$producto->precio;
                        KardexProducto::create([
                            'lugar' => "ALMACEN",
                            'tipo_registro' => "APERTURA ALMACEN", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                            'registro_id' => $existe->id,
                            'producto_id' => $producto->id,
                            'detalle' => "VALOR INICIAL POR APERTURA",
                            'precio' => $producto->precio,
                            'tipo_is' => 'INGRESO',
                            'cantidad_ingreso' => $cantidad,
                            'cantidad_saldo' => (float)$cantidad,
                            'cu' => $producto->precio,
                            'monto_ingreso' => $monto,
                            'monto_saldo' => $monto,
                            'fecha' => date('Y-m-d'),
                        ]);
                    } else {
                        $existe->update([
                            "stock_actual" => $cantidad,
                        ]);
                        $monto = (float)$cantidad * (float)$producto->precio;
                        $kardex = KardexProducto::where("lugar", "ALMACEN")
                            ->where("producto_id", $producto->id)
                            ->where("tipo_registro", "APERTURA ALMACEN")
                            ->get()->first();
                        if ($kardex) {
                            $kardex->update([
                                'lugar' => "ALMACEN",
                                'tipo_registro' => "APERTURA ALMACEN", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                                'registro_id' => $existe->id,
                                'producto_id' => $producto->id,
                                'detalle' => "VALOR INICIAL POR APERTURA",
                                'precio' => $producto->precio,
                                'tipo_is' => 'INGRESO',
                                'cantidad_ingreso' => $cantidad,
                                'cantidad_saldo' => (float)$cantidad,
                                'cu' => $producto->precio,
                                'monto_ingreso' => $monto,
                                'monto_saldo' => $monto,
                                'fecha' => date('Y-m-d'),
                            ]);
                        } else {
                            KardexProducto::create([
                                'lugar' => "ALMACEN",
                                'tipo_registro' => "APERTURA ALMACEN", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                                'registro_id' => $existe->id,
                                'producto_id' => $producto->id,
                                'detalle' => "VALOR INICIAL POR APERTURA",
                                'precio' => $producto->precio,
                                'tipo_is' => 'INGRESO',
                                'cantidad_ingreso' => $cantidad,
                                'cantidad_saldo' => (float)$cantidad,
                                'cu' => $producto->precio,
                                'monto_ingreso' => $monto,
                                'monto_saldo' => $monto,
                                'fecha' => date('Y-m-d'),
                            ]);
                        }
                    }
                } else {
                    $existe = SucursalStock::where("producto_id", $producto->id)->get()->first();
                    if (!$existe) {
                        $existe = SucursalStock::create([
                            "producto_id" => $producto->id,
                            "stock_actual" => $cantidad,
                        ]);
                        $monto = (float)$cantidad * (float)$producto->precio;
                        KardexProducto::create([
                            'lugar' => "SUCURSAL",
                            'tipo_registro' => "APERTURA SUCURSAL", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                            'registro_id' => $existe->id,
                            'producto_id' => $producto->id,
                            'detalle' => "VALOR INICIAL POR APERTURA",
                            'precio' => $producto->precio,
                            'tipo_is' => 'INGRESO',
                            'cantidad_ingreso' => $cantidad,
                            'cantidad_saldo' => (float)$cantidad,
                            'cu' => $producto->precio,
                            'monto_ingreso' => $monto,
                            'monto_saldo' => $monto,
                            'fecha' => date('Y-m-d'),
                        ]);
                    } else {
                        $existe->update([
                            "stock_actual" => $cantidad,
                        ]);
                        $monto = (float)$cantidad * (float)$producto->precio;
                        $kardex = KardexProducto::where("lugar", "SUCURSAL")
                            ->where("producto_id", $producto->id)
                            ->where("tipo_registro", "APERTURA SUCURSAL")
                            ->get()->first();
                        if ($kardex) {
                            $kardex->update([
                                'lugar' => "SUCURSAL",
                                'tipo_registro' => "APERTURA SUCURSAL", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                                'registro_id' => $existe->id,
                                'producto_id' => $producto->id,
                                'detalle' => "VALOR INICIAL POR APERTURA",
                                'precio' => $producto->precio,
                                'tipo_is' => 'INGRESO',
                                'cantidad_ingreso' => $cantidad,
                                'cantidad_saldo' => (float)$cantidad,
                                'cu' => $producto->precio,
                                'monto_ingreso' => $monto,
                                'monto_saldo' => $monto,
                                'fecha' => date('Y-m-d'),
                            ]);
                        } else {
                            KardexProducto::create([
                                'lugar' => "SUCURSAL",
                                'tipo_registro' => "APERTURA SUCURSAL", //INGRESO, EGRESO, VENTA, COMPRA,etc...
                                'registro_id' => $existe->id,
                                'producto_id' => $producto->id,
                                'detalle' => "VALOR INICIAL POR APERTURA",
                                'precio' => $producto->precio,
                                'tipo_is' => 'INGRESO',
                                'cantidad_ingreso' => $cantidad,
                                'cantidad_saldo' => (float)$cantidad,
                                'cu' => $producto->precio,
                                'monto_ingreso' => $monto,
                                'monto_saldo' => $monto,
                                'fecha' => date('Y-m-d'),
                            ]);
                        }
                    }
                }
            } else {
                $producto->precio = isset($precio) && $precio != '' ? $precio : 0;
                $producto->stock_min = isset($stock_min) && $stock_min != '' ? $stock_min : 0;
                $producto->save();
            }

            DB::commit();
            return response()->JSON([
                'sw' => true,
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

    public function destroy(ImportancionApertura $importacion_apertura)
    {
        DB::beginTransaction();
        try {
            $importacion_apertura->delete();
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

    public function importar_archivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls',
        ], [
            'archivo.required' => 'El campo archivo es requerido',
            'archivo.mimes' => 'Debes seleccionar un archivo valido :values',
        ]);


        DB::beginTransaction();
        try {
            $archivo = $request->file('archivo');
            $extension = '.' . $archivo->getClientOriginalExtension();

            if ($extension == '.xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }
            $spreadsheet = $reader->load($archivo);
            $fila = 6;
            // recorrer las filas del archivo
            $fecha_actual = date("Y-m-d");
            while (trim($spreadsheet->getSheet(0)->getCell('A' . $fila)->getValue()) != "") {
                // realizar el registro
                $nom_grupo = trim($spreadsheet->getSheet(0)->getCell('A' . $fila)->getValue());
                $existe_grupo = Grupo::where("nombre", $nom_grupo)->get()->first();
                if (!$existe_grupo) {
                    $existe_grupo = Grupo::create([
                        "nombre" => $nom_grupo,
                        "fecha_registro" => $fecha_actual
                    ]);
                }
                $producto = Producto::create([
                    "codigo" => $spreadsheet->getSheet(0)->getCell('B' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('B' . $fila)->getValue() : "0",
                    "nombre" => $spreadsheet->getSheet(0)->getCell('D' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('D' . $fila)->getValue() : "",
                    "medida" => $spreadsheet->getSheet(0)->getCell('C' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('C' . $fila)->getValue() : "",
                    "grupo_id" => $existe_grupo->id,
                    "precio" => $spreadsheet->getSheet(0)->getCell('E' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('E' . $fila)->getValue() : "0",
                    "precio_mayor" => $spreadsheet->getSheet(0)->getCell('E' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('E' . $fila)->getValue() : "0",
                    "stock_min" => 0,
                    "descontar_stock" => "SI",
                    "fecha_registro" => $fecha_actual
                ]);

                $stock_almacen = $spreadsheet->getSheet(0)->getCell('F' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('F' . $fila)->getValue() : "0";
                $stock_sucursal = $spreadsheet->getSheet(0)->getCell('G' . $fila)->getValue() != "" ? $spreadsheet->getSheet(0)->getCell('G' . $fila)->getValue() : "0";
                $stock_almacen = (float)$stock_almacen;
                $stock_sucursal = (float)$stock_sucursal;

                if ($stock_almacen > 0) {
                    KardexProducto::registroIngreso("ALMACEN", "INGRESO", 0, $producto, $stock_almacen, $producto->precio);
                }
                if ($stock_sucursal > 0) {
                    KardexProducto::registroIngreso("SUCURSAL", "INGRESO", 0, $producto, $stock_sucursal, $producto->precio);
                }

                $fila++;
            }

            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'IMPORTACIÓN DE ARCHIVO',
                'descripcion' => 'EL USUARIO ' . Auth::user()->usuario . 'IMPORTO UN ARCHIVO',
                'datos_original' => $fila-- . ' REGISTROS IMPORTADOS',
                'modulo' => 'IMPORTACIÓN DE APERTURA',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'msj' => 'El archivo se cargo correctamente'
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
