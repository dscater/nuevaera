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

    protected $appends = ["texto_lugar"];

    public function getTextoLugarAttribute()
    {
        $o_lugar = null;
        $texto = 'ALMACÉN';
        if ($this->lugar == 'SUCURSAL') {
            $texto = 'SUCURSAL ';
            $o_lugar = Sucursal::find($this->registro_id);
            $texto .= $o_lugar->nombre;
        }
        return $texto;
    }
}
