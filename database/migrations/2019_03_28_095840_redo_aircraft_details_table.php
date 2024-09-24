<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RedoAircraftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('aircraft_details');
        
        Schema::create('aircraft_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aircraft_fully_qualified_registration_no')->nullable();
            $table->string('aircraft_identification_no')->nullable();
            $table->string('manufacturer_name')->nullable();
            $table->string('manufacturer_code', 5)->index();
            $table->string('aircraft_model_identifier')->index();
            $table->string('aircraft_series_identifier')->nullable();
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
}
