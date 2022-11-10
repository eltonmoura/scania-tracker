<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmnilinkVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('omnilink_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('idTerminal', 50)->nullable();
            $table->integer('terminal')->nullable();
            $table->string('placa', 50)->nullable();
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
        Schema::dropIfExists('omnilink_vehicles');
    }
}
