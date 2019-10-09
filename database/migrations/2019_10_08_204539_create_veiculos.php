<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('veiid', 10);
            $table->string('placa', 7);
            $table->string('vs', 5)->nullable();
            $table->boolean('st1')->nullable();
            $table->boolean('st2')->nullable();
            $table->boolean('st3')->nullable();
            $table->integer('tcmd')->nullable();
            $table->boolean('tmac')->nullable();
            $table->boolean('ecmd')->nullable();
            $table->integer('tp')->nullable();
            $table->integer('ta')->nullable();
            $table->integer('eqp')->nullable();
            $table->string('mot', 40)->nullable();
            $table->string('prop', 50)->nullable();
            $table->boolean('die')->nullable();
            $table->boolean('ie')->nullable();
            $table->boolean('loc')->nullable();
            $table->string('ident', 50)->nullable();
            $table->boolean('vmanut')->nullable();
            $table->date('valespelhamento')->nullable();
            $table->boolean('propcancelamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('veiculos');
    }
}
