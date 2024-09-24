<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLocationsTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('locations')->truncate();
        
        Schema::table('locations', function(Blueprint $table){
            $table->dropColumn('cage_code');
        });
        
        Schema::table('locations', function(Blueprint $table){
            $table->string('sap_location_name')->after('id');
            $table->string('name')->unique()->change();
            $table->integer('plant_code')->unique()->after('name');
            
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('locations')->truncate();
        
        Schema::table('locations', function(Blueprint $table){
            $table->dropColumn('sap_location_name');
            $table->dropColumn('name');
            $table->dropColumn('plant_code');
        });
        
        Schema::table('locations', function(Blueprint $table){
            $table->string('cage_code')->unique();
        });
        
        Schema::enableForeignKeyConstraints();
    }
}