<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveExcludedPartsColumnFromShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_findings', function(Blueprint $table){
            $table->dropColumn('excluded_part');
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
            $table->boolean('excluded_part')->nullable()->default(0)->after('is_valid');
        });
    }
}