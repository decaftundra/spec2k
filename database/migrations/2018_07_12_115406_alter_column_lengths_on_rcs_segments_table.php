<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnLengthsOnRcsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('RCS_Segments', function(Blueprint $table)
        {
            $table->string('USN', 35)->nullable()->change(); // rcsUSN	Removed Universal Serial Number	Universal Serial Number	USN	N	String	6/35
            $table->text('PML', 1000)->nullable()->change(); // rcsPML	Removed Part Modificiation Level	Part Modification Level	PML	N	String	1/1000
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('RCS_Segments', function(Blueprint $table)
        {
            $table->string('USN', 20)->nullable()->change(); // rcsUSN	Removed Universal Serial Number	Universal Serial Number	USN	N	String	6/20
            $table->string('PML', 100)->nullable()->change(); // rcsPML	Removed Part Modificiation Level	Part Modification Level	PML	N	String	1/100
        });
        
        
    }
}
