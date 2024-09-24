<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function(Blueprint $table){
            $table->index(['status']);
            $table->index(['shipped_at']);
            $table->index(['scrapped_at']);
            $table->index(['subcontracted_at']);
            $table->index(['standby_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
            $table->index(['deleted_at']);
            $table->index(['rcsMPN']);
            $table->index(['rcsSER']);
            $table->index(['rcsMRD']);
            $table->index(['hdrROC']);
            $table->index(['hdrRON']);
            $table->index(['plant_code', 'deleted_at']);
        });
        
        Schema::table('notification_piece_parts', function(Blueprint $table){
            $table->index(['deleted_at']);
            $table->index(['wpsSFI']);
        });
        
        Schema::table('shop_findings', function(Blueprint $table){
            $table->index(['planner_group']);
            $table->index(['deleted_at']);
            $table->index(['standby_at']);
            $table->index(['status']);
            $table->index(['is_valid']);
            $table->index(['shipped_at']);
            $table->index(['scrapped_at']);
            $table->index(['subcontracted_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
            $table->index(['plant_code', 'deleted_at']);
        });
        
        Schema::table('locations', function(Blueprint $table){
            $table->index(['sap_location_name']);
        });
        
        Schema::table('RCS_Segments', function(Blueprint $table){
            $table->index(['MPN']);
            $table->index(['SER']);
            $table->index(['MRD']);
        });
        
        Schema::table('HDR_Segments', function(Blueprint $table){
            $table->index(['ROC']);
            $table->index(['RON']);
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table){
            $table->index(['SFI']);
        });
        
        Schema::table('piece_part_details', function(Blueprint $table){
            $table->index(['deleted_at']);
        });
        
        Schema::table('aircraft_details', function(Blueprint $table){
            $table->index(['aircraft_fully_qualified_registration_no']);
        });
        
        Schema::table('maintenance_notices', function(Blueprint $table){
            $table->index(['display']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_notices', function(Blueprint $table){
            $table->dropIndex(['display']);
        });
        
        Schema::table('aircraft_details', function(Blueprint $table){
            $table->dropIndex(['aircraft_fully_qualified_registration_no']);
        });
        
        Schema::table('piece_part_details', function(Blueprint $table){
            $table->dropIndex(['deleted_at']);
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table){
            $table->dropIndex(['SFI']);
        });
        
        Schema::table('HDR_Segments', function(Blueprint $table){
            $table->dropIndex(['ROC']);
            $table->dropIndex(['RON']);
        });
        
        Schema::table('RCS_Segments', function(Blueprint $table){
            $table->dropIndex(['MPN']);
            $table->dropIndex(['SER']);
            $table->dropIndex(['MRD']);
        });
        
        Schema::table('locations', function(Blueprint $table){
            $table->dropIndex(['sap_location_name']);
        });
        
        Schema::table('shop_findings', function(Blueprint $table){
            $table->dropIndex(['planner_group']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['standby_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_valid']);
            $table->dropIndex(['shipped_at']);
            $table->dropIndex(['scrapped_at']);
            $table->dropIndex(['subcontracted_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['plant_code', 'deleted_at']);
        });
        
        Schema::table('notification_piece_parts', function(Blueprint $table){
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['wpsSFI']);
        });
        
        Schema::table('notifications', function(Blueprint $table){
            $table->dropIndex(['status']);
            $table->dropIndex(['shipped_at']);
            $table->dropIndex(['scrapped_at']);
            $table->dropIndex(['subcontracted_at']);
            $table->dropIndex(['standby_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['rcsMPN']);
            $table->dropIndex(['rcsSER']);
            $table->dropIndex(['rcsMRD']);
            $table->dropIndex(['hdrROC']);
            $table->dropIndex(['hdrRON']);
            $table->dropIndex(['plant_code', 'deleted_at']);
        });
    }
}