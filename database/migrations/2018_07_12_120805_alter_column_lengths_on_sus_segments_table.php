<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnLengthsOnSusSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('SUS_Segments', function(Blueprint $table)
        {
            $table->string('SER', 30)->change(); // susSER	Shipped Manufacturer Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->string('PNR', 32)->nullable()->change(); // susPNR	Shipped Manufacturer Part Number	Part Number	PNR	N	String	1/15
        	$table->string('USN', 35)->nullable()->change(); // susUSN	Shipped Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->text('PML', 1000)->nullable()->change(); // susPML	Shipped Part Modification Level	Part Modification Level	PML	N	String	1/100
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('SUS_Segments', function(Blueprint $table)
        {
            $table->string('SER', 15)->change(); // susSER	Shipped Manufacturer Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->string('PNR', 15)->nullable()->change(); // susPNR	Shipped Manufacturer Part Number	Part Number	PNR	N	String	1/15
        	$table->string('USN', 20)->nullable()->change(); // susUSN	Shipped Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('PML', 100)->nullable()->change(); // susPML	Shipped Part Modification Level	Part Modification Level	PML	N	String	1/100
        });
    }
}
