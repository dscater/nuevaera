<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportancionAperturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importancion_aperturas', function (Blueprint $table) {
            $table->id();
            $table->string("lugar");
            $table->unsignedBigInteger("registro_id");
            $table->bigInteger("total_registros");
            $table->integer("cambio_stock");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importancion_aperturas');
    }
}
