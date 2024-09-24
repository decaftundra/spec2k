<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnginePositionToAircraftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft_details', function (Blueprint $table) {
            $table->string('engine_position_identifier', 25)->default('UNK')->after('engines_series');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft_details', function (Blueprint $table) {
            $table->dropColumn('engine_position_identifier');
        });
    }
}
