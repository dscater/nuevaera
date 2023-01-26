<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAccion extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "accion", "descripcion", "datos_original", "datos_nuevo", "modulo", "fecha", "hora",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
