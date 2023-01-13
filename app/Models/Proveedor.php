<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $fillable = [
        "nombre", "nit", "dir", "fono", "nombre_contacto",
        "descripcion", "fecha_registro",
    ];
}
