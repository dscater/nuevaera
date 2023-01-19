<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciaProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia_productos', function (Blueprint $table) {
            $table->id();
            $table->string("origen", 155);
            $table->unsignedBigInteger("origen_id")->nullable();
            $table->string("destino", 155);
            $table->unsignedBigInteger("destino_id")->nullable();
            $table->unsignedBigInteger("producto_id");
            $table->integer("cantidad");
            $table->text("descripcion")->nullable();
            $table->date("fecha_registro");
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
        Schema::dropIfExists('transferencia_productos');
    }
}
