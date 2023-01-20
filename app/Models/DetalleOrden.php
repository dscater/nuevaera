<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $fillable = [
        "orden_id", "producto_id", "sucursal_stock_id", "cantidad", "venta_mayor",
        "precio", "subtotal",
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function sucursal_stock()
    {
        return $this->belongsTo(SucursalStock::class, 'sucursal_stock_id');
    }

    public function detalle_orden()
    {
        return $this->hasOne(DetalleOrden::class, 'detalle_orden_id');
    }
}
