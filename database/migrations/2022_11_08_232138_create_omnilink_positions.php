<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmnilinkPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omnilink_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('numeroSequencia');
            $table->string('idTerminal', 50)->nullable();
            $table->dateTime('dataHoraEvento')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->string('localizacao', 150)->nullable();
            $table->string('idSeqVeiculo', 50)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 5)->nullable();
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
        Schema::dropIfExists('omnilink_positions');
    }
}
