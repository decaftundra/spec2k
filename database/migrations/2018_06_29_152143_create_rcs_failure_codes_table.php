<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcsFailureCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcs_failure_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('RRC', 1);
            $table->string('FFC', 2);
            $table->string('FFI', 2);
            $table->string('FHS', 2);
            $table->string('FCR', 2);
            $table->string('FAC', 2);
            $table->string('FBC', 2);
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
        Schema::dropIfExists('rcs_failure_codes');
    }
}
