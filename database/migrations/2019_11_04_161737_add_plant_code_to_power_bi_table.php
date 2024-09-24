<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlantCodeToPowerBiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('power_bi_shop_findings', function(Blueprint $table){
            $table->integer('plant_code')->index()->nullable()->default(NULL)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('power_bi_shop_findings', function(Blueprint $table){
            $table->dropColumn('plant_code');
        });
    }
}
