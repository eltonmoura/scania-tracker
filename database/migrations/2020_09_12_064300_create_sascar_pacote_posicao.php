<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSascarPacotePosicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sascar_pacote_posicao', function (Blueprint $table) {
            $table->bigIncrements('idPacote');
            $table->unsignedBigInteger('idVeiculo');
            $table->unsignedBigInteger('integradoraId');
            $table->tinyInteger('ignicao')->nullable();
            $table->integer('horimetro')->nullable();
            $table->integer('odometro')->nullable();
            $table->integer('rpm')->nullable();
            $table->integer('velocidade')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('rua', 100)->nullable();
            $table->string('uf', 10)->nullable();
            $table->dateTime('dataPacote')->nullable();
            $table->dateTime('dataPosicao')->nullable();

            /*
            // pode ser que um pacote seja importado antes que o veículo e isso
            // causaria um erro
            $table->foreign('idVeiculo')
                ->references('idVeiculo')
                ->on('sascar_veiculos');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sascar_pacote_posicao');
    }
}
