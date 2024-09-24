<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlannerGroupToShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_findings', function(Blueprint $table){
            $table->string('planner_group')->nullable()->default(null)->after('SFVersion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_findings', function(Blueprint $table){
            $table->dropColumn('planner_group');
        });
    }
}