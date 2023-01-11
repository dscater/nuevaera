<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex_productos', function (Blueprint $table) {
            $table->id();
            $table->string("lugar");
            $table->unsignedBigInteger("registro_id");
            $table->unsignedBigInteger("producto_id");
            $table->text("detalle");
            $table->decimal("precio", 24, 2);
            $table->string("tipo_is");
            $table->integer("ingreso");
            $table->integer("salida");
            $table->decimal("cu", 24, 2);
            $table->decimal("monto_ingreso", 24, 2);
            $table->decimal("monto_salida", 24, 2);
            $table->decimal("monto_saldo", 24, 2);
            $table->timestamps();

            $table->foreign("producto_id")->on("productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kardex_productos');
    }
}
