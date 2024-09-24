<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutosaveColumnToSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('HDR_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('AID_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('API_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('ATT_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('EID_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('LNK_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('Misc_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('RCS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('RLS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('SAS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('SPT_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('SUS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('NHS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table){
            $table->dateTime('autosaved_at')->nullable()->after('validated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('HDR_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('AID_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('API_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('ATT_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('EID_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('LNK_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('Misc_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('RCS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('RLS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('SAS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('SPT_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('SUS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('NHS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table){
            $table->dropColumn('autosaved_at');
        });
    }
}
