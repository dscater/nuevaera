<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    protected $fillable = [
        "orden_id",
        "estado",
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_id');
    }
}
