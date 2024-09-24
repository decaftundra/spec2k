<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPiecePartIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('NHS_Segments', function(Blueprint $table)
        {
            $table->dropForeign('nhs_segments_piece_part_detail_id_foreign');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table)
        {
            $table->dropForeign('wps_segments_piece_part_detail_id_foreign');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table)
        {
            $table->dropForeign('rps_segments_piece_part_detail_id_foreign');
        });
        
        Schema::table('piece_part_details', function(Blueprint $table)
        {
            $table->string('id')->index()->unique()->change();
        });
        
        Schema::table('NHS_Segments', function(Blueprint $table)
        {
            $table->string('piece_part_detail_id')->index()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table)
        {
            $table->string('piece_part_detail_id')->index()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table)
        {
            $table->string('piece_part_detail_id')->index()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
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
        /*Schema::disableForeignKeyConstraints();
        
        Schema::table('NHS_Segments', function(Blueprint $table)
        {
            $table->dropIndex('nhs_segments_piece_part_detail_id_index');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table)
        {
            $table->dropIndex('wps_segments_piece_part_detail_id_index');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table)
        {
            $table->dropIndex('rps_segments_piece_part_detail_id_index');
        });
        
        Schema::table('piece_part_details', function(Blueprint $table)
        {
            $table->dropPrimary('piece_part_details_id_primary');
            $table->dropUnique('piece_part_details_id_unique');
            
            $table->increments('id')->change();
        });
        
        Schema::table('NHS_Segments', function(Blueprint $table)
        {
            $table->integer('piece_part_detail_id')->unsigned()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
        });
        
        Schema::table('WPS_Segments', function(Blueprint $table)
        {
            $table->integer('piece_part_detail_id')->unsigned()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
        });
        
        Schema::table('RPS_Segments', function(Blueprint $table)
        {
            $table->integer('piece_part_detail_id')->unsigned()->change();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
        });
        
        Schema::enableForeignKeyConstraints();
        */
    }
}