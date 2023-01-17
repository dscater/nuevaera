<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "lugar", "lugar_id", "tipo_registro", "registro_id",
        "producto_id", "detalle", "precio", "tipo_is",
        "cantidad_ingreso", "cantidad_salida", "cantidad_saldo", "cu",
        "monto_ingreso", "monto_salida", "monto_saldo", "fecha",
    ];

    // REGISTRAR INGRESO
    public static function registroIngreso($lugar, $lugar_id = 0, $tipo_registro, $registro_id = 0, Producto $producto, $cantidad, $precio, $detalle = "")
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->where("lugar", $lugar)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $monto = (float)$cantidad * (float)$precio;
        if ($ultimo) {
            if (!$detalle || $detalle == "") {
                $detalle = "INGRESO DE PRODUCTO";
            }
            KardexProducto::create([
                'lugar' => $lugar,
                'lugar_id' => $lugar_id,
                'tipo_registro' => $tipo_registro, //INGRESO, EGRESO, VENTA, COMPRA,etc...
                'registro_id' => $registro_id,
                'producto_id' => $producto->id,
                'detalle' => $detalle,
                'precio' => $precio,
                'tipo_is' => 'INGRESO',
                'cantidad_ingreso' => $cantidad,
                'cantidad_saldo' => (float)$ultimo->cantidad_saldo + (float)$cantidad,
                'cu' => $producto->precio,
                'monto_ingreso' => $monto,
                'monto_saldo' => (float)$ultimo->monto_saldo + $monto,
                'fecha' => date('Y-m-d'),
            ]);
        } else {
            $detalle = "VALOR INICIAL";
            KardexProducto::create([
                'lugar' => $lugar,
                'lugar_id' => $lugar_id,
                'tipo_registro' => $tipo_registro, //INGRESO, EGRESO, VENTA, NULL,etc...
                'registro_id' => $registro_id,
                'producto_id' => $producto->id,
                'detalle' => $detalle,
                'precio' => $precio,
                'tipo_is' => 'INGRESO',
                'cantidad_ingreso' => $cantidad,
                'cantidad_saldo' => (float)$cantidad,
                'cu' => $producto->precio,
                'monto_ingreso' => $monto,
                'monto_saldo' =>  $monto,
                'fecha' => date('Y-m-d'),
            ]);
        }

        // INCREMENTAR STOCK
        $producto->almacen->stock_actual = (float)$producto->almacen->stock_actual + $cantidad;
        $producto->almacen->save();
        return true;
    }

    // REGISTRAR EGRESO
    public static function registroEgreso($lugar, $lugar_id = 0, $tipo_registro, $registro_id = 0, Producto $producto, $cantidad, $precio, $detalle = "")
    {
        //buscar el ultimo registro y usar sus valores
        $ultimo = KardexProducto::where('producto_id', $producto->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->last();
        $monto = (float)$cantidad * (float)$precio;

        if (!$detalle || $detalle == "") {
            $detalle = "SALIDA DE PRODUCTO";
        }

        KardexProducto::create([
            'lugar' => $lugar,
            'lugar_id' => $lugar_id,
            'tipo_registro' => $tipo_registro,
            'registro_id' => $registro_id,
            'producto_id' => $producto->id,
            'detalle' => $detalle,
            'precio' => $precio,
            'tipo_is' => 'EGRESO',
            'cantidad_salida' => $cantidad,
            'cantidad_saldo' => (float)$ultimo->cantidad_saldo - (float)$cantidad,
            'cu' => $producto->precio,
            'monto_salida' => $monto,
            'monto_saldo' => (float)$ultimo->monto_saldo - $monto,
            'fecha' => date('Y-m-d'),
        ]);

        if ($producto->descontar_stock == 'SI') {
            // DECREMENTAR STOCK
            $producto->almacen->stock_actual = (float)$producto->almacen->stock_actual - $cantidad;
            $producto->almacen->save();
        }

        return true;
    }


    // ACTUALIZA REGISTROS KARDEX
    // FUNCIÓN QUE ACTUALIZA LOS REGISTROS DEL KARDEX DE UN LUGAR
    // SOLO ACTUALIZARA LOS REGISTROS POSTERIORES AL REGISTRO ACTUALIZADO ó AL ELIMINAR UN REGISTRO
    public static function actualizaRegistrosKardex($id, $lugar, $lugar_id = 0)
    {
        $kardex = KardexProducto::find($id);
        $siguientes = KardexProducto::where("lugar", $lugar)->where("id", ">=", $id)->get();
        if ($lugar != 'ALMACEN') {
            $siguientes = KardexProducto::where("lugar", $lugar)->where("lugar_id", $lugar_id)->where("id", ">", $id)->get();
        }
        foreach ($siguientes as $item) {
            $anterior = KardexProducto::where("lugar", $lugar)->where("id", "<", $item->id)->get()->last();
            if ($item->tipo_is == 'INGRESO') {
                $ingreso_producto = IngresoProducto::find($item->registro_id);
                $monto = (float)$ingreso_producto->cantidad * (float)$ingreso_producto->precio_compra;
                if ($anterior) {
                    $item->update([
                        'precio' => $ingreso_producto->precio_compra,
                        'cantidad_ingreso' => $ingreso_producto->cantidad,
                        'cantidad_saldo' => (float)$anterior->cantidad_saldo + (float)$ingreso_producto->cantidad,
                        'cu' => $ingreso_producto->producto->precio,
                        'monto_ingreso' => $monto,
                        'monto_saldo' => (float)$anterior->monto_saldo + $monto,
                    ]);
                } else {
                    $item->update([
                        'precio' => $ingreso_producto->precio_compra,
                        'cantidad_ingreso' => $ingreso_producto->cantidad,
                        'cantidad_saldo' => (float)$ingreso_producto->cantidad,
                        'cu' => $ingreso_producto->producto->precio,
                        'monto_ingreso' => $monto,
                        'monto_saldo' => $monto,
                    ]);
                }
            } else {
                $ingreso_producto = IngresoProducto::find($item->registro_id);
                $monto = (float)$ingreso_producto->cantidad * (float)$ingreso_producto->precio_compra;
                if ($anterior) {
                } else {
                }
                $item->update([
                    'precio' => $ingreso_producto->precio_compra,
                    'cantidad_salida' => $ingreso_producto->cantidad,
                    'cantidad_saldo' => (float)$anterior->cantidad_saldo - (float)$ingreso_producto->cantidad,
                    'cu' => $ingreso_producto->producto->precio,
                    'monto_salida' => $monto,
                    'monto_saldo' => (float)$anterior->monto_saldo - $monto,
                ]);
            }
        }
    }
}
