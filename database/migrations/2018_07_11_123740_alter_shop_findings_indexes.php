<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopFindingsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        
        // drop the foreign key indexes
        
        Schema::table('HDR_Segments', function(Blueprint $table)
        {
            $table->dropForeign('hdr_segments_shop_finding_id_foreign');
        });
        
        Schema::table('shop_findings_details', function(Blueprint $table)
        {
            $table->dropForeign('shop_findings_details_shop_finding_id_foreign');
        });
        
        Schema::table('piece_parts', function(Blueprint $table)
        {
            $table->dropForeign('piece_parts_shop_finding_id_foreign');
        });
        
        // change indexes
        
        Schema::table('shop_findings', function(Blueprint $table)
        {
            $table->string('id')->index()->unique()->change();
        });
        
        Schema::table('shop_findings_details', function(Blueprint $table)
        {
            $table->string('shop_finding_id')->index()->unique()->change();
        });
        
        // redefine foreign keys
        
        Schema::table('HDR_Segments', function(Blueprint $table)
        {
            $table->string('shop_finding_id')->index()->change();
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
        });
        
        Schema::table('piece_parts', function(Blueprint $table)
        {
            $table->string('shop_finding_id')->index()->change();
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
        });
        
        Schema::table('shop_findings_details', function(Blueprint $table)
        {
            $table->string('shop_finding_id')->index()->change();
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
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
        //
    }
}
