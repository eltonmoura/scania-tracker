<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMensagemCb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensagem_cb', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mid');
            $table->bigInteger('veiid');
            $table->dateTime('dt');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lon', 11, 8)->nullable();
            $table->string('mun', 100)->nullable();
            $table->string('uf', 10)->nullable();
            $table->string('rod', 100)->nullable();
            $table->string('rua', 100)->nullable();
            $table->integer('vel')->nullable();
            $table->integer('evt4')->nullable();
            $table->boolean('evt13')->nullable();
            $table->integer('ori')->nullable();
            $table->integer('tpmsg')->nullable();
            $table->dateTime('dtinc')->nullable();
            $table->integer('evtg')->nullable();
            $table->integer('odm')->nullable();
            $table->integer('bat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensagem_cb');
    }
}
