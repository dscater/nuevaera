<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSucursalUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucursal_usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("sucursal_id");
            $table->unsignedBigInteger("caja_id");
            $table->timestamps();

            $table->foreign("user_id")->on("users")->references("id");
            $table->foreign("sucursal_id")->on("sucursals")->references("id");
            $table->foreign("caja_id")->on("cajas")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucursal_usuarios');
    }
}
