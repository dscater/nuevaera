<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportancionApertura extends Model
{
    use HasFactory;

    protected $fillable = [
        "lugar",
        "registro_id",
        "total_registros",
        "cambio_stock",
    ];
}
