<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAircraftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_details', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('operator')->nullable();
            $table->string('registration')->nullable();
            $table->string('aircraft_sub_series')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('status')->nullable();
            $table->string('engine_sub_series')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('age')->nullable();
            $table->string('cumulative_hours')->nullable();
            $table->string('previous_12_months_hours')->nullable();
            $table->string('cumulative_cycles')->nullable();
            $table->string('previous_12_months_cycles')->nullable();
            $table->string('manager')->nullable();
            $table->string('hours_and_cycles_date')->nullable();
            $table->string('series')->nullable();
            $table->string('master_series')->nullable();
            $table->string('engine_family')->nullable();
            $table->string('engine_manufacturer')->nullable();
            $table->string('engine_series')->nullable();
            $table->string('number_of_engines')->nullable();
            
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
        Schema::dropIfExists('aircraft_details');
    }
}
