<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSascarVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sascar_veiculos', function (Blueprint $table) {
            $table->bigIncrements('idVeiculo');
            $table->unsignedBigInteger('idCliente');
            $table->string('placa', 7);
            $table->tinyInteger('portaBloqueio')->nullable();
            $table->tinyInteger('portaPanico')->nullable();
            $table->boolean('satelital')->nullable();
            $table->boolean('telemetria')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sascar_veiculos');
    }
}
