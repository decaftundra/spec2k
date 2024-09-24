<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtasCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utas_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('MANDT');
            $table->integer('PLANT');
            $table->string('MATNR');
            $table->string('SUB');
            $table->string('COMP');
            $table->string('FEAT')->nullable();
            $table->string('DESCR');
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
        Schema::dropIfExists('utas_codes');
    }
}
