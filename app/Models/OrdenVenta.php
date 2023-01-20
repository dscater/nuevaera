<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        "sucursal_id", "caja_id", "cliente_id", "nit", "total", "fecha_registro",
    ];

    protected $appends = ["editable"];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalle_ordens()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }

    public function devolucion()
    {
        return $this->hasOne(Devolucion::class, 'orden_id');
    }

    public function getEditableAttribute()
    {
        if ($this->devolucion) {
            return false;
        }
        return true;
    }
}
