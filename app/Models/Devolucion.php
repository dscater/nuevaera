<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $fillable = ["orden_id", "fecha_registro",];

    public function orden()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_id');
    }

    public function devolucion_detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'devolucion_id');
    }
}
