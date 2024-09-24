<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEngineDetailColumnsToAircraftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft_details', function (Blueprint $table) {
            $table->string('engine_manufacturer')->nullable()->after('aircraft_series_identifier');
            $table->string('engine_manufacturer_code')->nullable()->after('engine_manufacturer');
            $table->string('engine_type', 20)->nullable()->after('engine_manufacturer_code'); // AET | Aircraft Engine Type | Aircraft Engine/APU Type | Y | String | 1/20 | PW4000
            $table->string('engines_series', 32)->nullable()->after('engine_type'); // AEM | Aircraft Engine Model | Aircraft Engine/APU Model | Y | String | 1/32 | PW4056
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
            $table->dropColumn('engine_manufacturer');
            $table->dropColumn('engine_manufacturer_code');
            $table->dropColumn('engine_type');
            $table->dropColumn('engines_series');
        });
    }
}
