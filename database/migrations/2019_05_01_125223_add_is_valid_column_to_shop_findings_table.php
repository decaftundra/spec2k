<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsValidColumnToShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_findings', function(Blueprint $table){
            $table->boolean('is_valid')->nullable()->after('status');
            $table->dateTime('validated_at')->nullable()->after('status');
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
            $table->dropColumn(['is_valid', 'validated_at']);
        });
    }
}