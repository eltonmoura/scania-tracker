<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSascarVeiculosModifyPlaca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sascar_veiculos', function (Blueprint $table) {
            $table->string('placa', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sascar_veiculos', function (Blueprint $table) {
            $table->string('placa', 7)->change();
        });
    }
}
