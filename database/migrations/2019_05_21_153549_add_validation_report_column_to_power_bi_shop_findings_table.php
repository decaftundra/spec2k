<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValidationReportColumnToPowerBiShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('power_bi_shop_findings', function(Blueprint $table){
            $table->text('validation_report')->nullable()->after('ready_to_export');
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
            $table->dropColumn('validation_report');
        });
    }
}