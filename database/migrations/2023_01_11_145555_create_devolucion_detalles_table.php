<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolucionDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolucion_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("devolucion_id");
            $table->unsignedBigInteger("producto_id");
            $table->integer("cantidad");
            $table->timestamps();

            $table->foreign("devolucion_id")->on("devolucions")->references("id");
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
        Schema::dropIfExists('devolucion_detalles');
    }
}
