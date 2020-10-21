<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutotracPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autotrac_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('AccountNumber');
            $table->string('VehicleName', 50);
            $table->string('VehicleAddress', 50)->nullable();
            $table->tinyInteger('VehicleIgnition')->nullable();
            $table->integer('Velocity')->nullable();
            $table->integer('Odometer')->nullable();
            $table->integer('Hourmeter')->nullable();
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 10, 8)->nullable();
            $table->string('Landmark', 100)->nullable();
            $table->string('UF', 2)->nullable();
            $table->string('CountryDescription', 50)->nullable();
            $table->dateTime('PositionTime')->nullable();
            $table->tinyInteger('Direction')->nullable();
            $table->string('DirectionGPS', 50)->nullable();
            $table->string('Distance', 50)->nullable();
            $table->dateTime('ReceivedTime')->nullable();
            $table->tinyInteger('TransmissionChannel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autotrac_positions');
    }
}
