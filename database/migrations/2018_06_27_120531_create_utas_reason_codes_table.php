<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtasReasonCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utas_reason_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('MANDT');
            $table->integer('PLANT');
            $table->string('TYPE');
            $table->string('REASON');
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
        Schema::dropIfExists('utas_reason_codes');
    }
}
