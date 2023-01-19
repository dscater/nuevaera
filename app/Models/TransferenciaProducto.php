<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "origen",
        "origen_id",
        "destino",
        "destino_id",
        "producto_id",
        "cantidad",
        "descripcion",
        "fecha_registro",
    ];

    protected $appends = ["texto_origen", "texto_destino", "nombre_producto"];

    public function getNombreProductoAttribute()
    {
        return $this->producto->nombre;
    }

    public function o_origen()
    {
        return $this->belongsTo(Sucursal::class, 'origen_id');
    }

    public function o_destino()
    {
        return $this->belongsTo(Sucursal::class, 'destino_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function getTextoOrigenAttribute()
    {
        $o_origen = null;
        $texto = 'ALMACÉN';
        if ($this->origen == 'SUCURSAL') {
            $texto = 'SUCURSAL ';
            $o_origen = Sucursal::find($this->origen_id);
            $texto .= $o_origen->nombre;
        }
        return $texto;
    }

    public function getTextoDestinoAttribute()
    {
        $o_destino = null;
        $texto = 'ALMACÉN';
        if ($this->destino == 'SUCURSAL') {
            $texto = 'SUCURSAL ';
            $o_destino = Sucursal::find($this->destino_id);
            $texto .= $o_destino->nombre;
        }
        return $texto;
    }

    // FUNCIONES UTILIZADAS EN LA ACTUALIZACIÓN DE REGISTROS
    public static function restablecerRegistros($transferencia_producto, $origen, $origen_id = 0)
    {
        // RESTABLECER VALORES DEL ANTERIOR REGISTRO
        if ($transferencia_producto->origen == 'ALMACEN') {
            $eliminar_kardex = KardexProducto::where("lugar", "ALMACEN")
                ->where("tipo_registro", "TRANSFERENCIA")
                ->where("registro_id", $transferencia_producto->id)
                ->where("producto_id", $transferencia_producto->producto_id)
                ->get()
                ->first();
            $id_kardex = $eliminar_kardex->id;
            $eliminar_kardex->delete();

            $anterior = KardexProducto::where("lugar", "ALMACEN")
                ->where("id", "<", $id_kardex)
                ->get()
                ->last();
            $actualiza_desde = null;
            if ($anterior) {
                $actualiza_desde = $anterior;
            } else {
                // comprobar si existen registros posteriorres al actualizado
                $siguiente = KardexProducto::where("lugar", "ALMACEN")
                    ->where("id", ">", $id_kardex)
                    ->get()->first();
                if ($siguiente)
                    $actualiza_desde = $siguiente;
            }

            if ($actualiza_desde) {
                // actualizar a partir de este registro los sgtes. registros
                // KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, "ALMACEN");
            }
        } else {
        }

        if ($origen == 'ALMACEN') {
        }
    }
}
