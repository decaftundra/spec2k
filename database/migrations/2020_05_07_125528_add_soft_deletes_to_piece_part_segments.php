<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToPiecePartSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wps_segments', function(Blueprint $table){
            $table->softDeletes()->after('updated_at');
        });
        
        Schema::table('nhs_segments', function(Blueprint $table){
            $table->softDeletes()->after('updated_at');
        });
        
        Schema::table('rps_segments', function(Blueprint $table){
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wps_segments', function(Blueprint $table){
            $table->dropColumn('deleted_at');
        });
        
        Schema::table('nhs_segments', function(Blueprint $table){
            $table->dropColumn('deleted_at');
        });
        
        Schema::table('rps_segments', function(Blueprint $table){
            $table->dropColumn('deleted_at');
        });
    }
}