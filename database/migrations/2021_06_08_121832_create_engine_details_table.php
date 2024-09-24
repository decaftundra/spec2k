<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngineDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engine_details', function (Blueprint $table) {
            $table->id();
            $table->string('engine_manufacturer')->nullable();
            $table->string('engine_manufacturer_code')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('engines_series')->nullable();
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
        Schema::dropIfExists('engine_details');
    }
}
