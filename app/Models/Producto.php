<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        "codigo", "nombre", "medida", "grupo_id", "precio",
        "precio_mayor", "stock_min", "descontar_stock",
        "fecha_registro",
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function almacen()
    {
        return $this->hasOne(Almacen::class, 'producto_id');
    }

    public function stock_almacen()
    {
        return $this->hasOne(Almacen::class, 'producto_id');
    }

    public function stock_sucursal()
    {
        return $this->hasMany(SucursalStock::class, 'producto_id');
    }

    // FUNCIONES PARA INCREMETAR Y DECREMENTAR EL STOCK
    public static function incrementarStock($producto, $cantidad, $lugar, $lugar_id = 0)
    {
        if ($lugar == 'ALMACEN') {
            if (!$producto->almacen) {
                $producto->almacen()->create([
                    "stock_actual" => $cantidad
                ]);
            } else {
                $producto->almacen->stock_actual = (float)$producto->almacen->stock_actual + $cantidad;
                $producto->almacen->save();
            }
        } else {
            $stock_sucursal = $producto->stock_sucursal()->where("sucursal_id", $lugar_id)->get()->first();
            if (!$stock_sucursal) {
                $producto->stock_sucursal()->create([
                    "sucursal_id" => $lugar_id,
                    "stock_actual" => $cantidad
                ]);
            } else {
                $stock_sucursal->stock_actual = (float)$stock_sucursal->stock_actual + $cantidad;
                $stock_sucursal->save();
            }
        }
        return true;
    }
    public static function decrementarStock($producto, $cantidad, $lugar, $lugar_id = 0)
    {
        if ($producto->descontar_stock == 'SI') {
            if ($lugar == 'ALMACEN') {
                $producto->almacen->stock_actual = (float)$producto->almacen->stock_actual - $cantidad;
                $producto->almacen->save();
            } else {
                $stock_sucursal = $producto->stock_sucursal()->where("sucursal_id", $lugar_id)->get()->first();
                $stock_sucursal->stock_actual = (float)$stock_sucursal->stock_actual - $cantidad;
                $stock_sucursal->save();
            }
        }
        return true;
    }
}
