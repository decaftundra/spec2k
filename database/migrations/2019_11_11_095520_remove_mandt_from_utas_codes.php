<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMandtFromUtasCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utas_codes', function(Blueprint $table){
            $table->dropColumn('MANDT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utas_codes', function(Blueprint $table){
            $table->integer('MANDT')->nullable()->default(NULL)->after('id');
        });
    }
}