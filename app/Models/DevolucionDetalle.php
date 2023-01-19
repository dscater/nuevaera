<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        "devolucion_id", "detalle_orden_id", "producto_id", "sucursal_stock_id", "cantidad",
    ];

    protected $appends = ["cantidad_detalle_orden"];

    public function getCantidadDetalleOrdenAttribute()
    {
        return $this->detalle_orden ? $this->detalle_orden->cantidad : 0;
    }

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function detalle_orden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    public function sucursal_stock()
    {
        return $this->belongsTo(SucursalStock::class, 'sucursal_stock_id');
    }
}
