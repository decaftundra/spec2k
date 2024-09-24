<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSusSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SUS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->dateTime('SHD'); // susSHD	Shipped Date	Shipped Date	SHD	Y	Date	YYYY-MM-DD
        	$table->string('MFR', 5); // susMFR	Shipped Part Manufacturer Code	Manufacturer Code	MFR	Y	String	5/5
        	$table->string('MPN', 32); // susMPN	Shipped Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	Y	String	1/32
        	$table->string('SER', 15); // susSER	Shipped Manufacturer Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->string('MFN', 55)->nullable(); // susMFN	Shipped Part Manufacturer Name	Manufacturer Name	MFN	N	String	1/55	Honeywell
        	$table->string('PDT', 100)->nullable(); // susPDT	Shipped Manufacturer Part Description	Part Description	PDT	N	String	1/100
        	$table->string('PNR', 15)->nullable(); // susPNR	Shipped Manufacturer Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // susOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // susUSN	Shipped Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('ASN', 32)->nullable(); // susASN	Shipped Operator Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // susUCN	Shipped Operator Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // susSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // susUST	Shipped Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
        	$table->string('PML', 100)->nullable(); // susPML	Shipped Part Modification Level	Part Modification Level	PML	N	String	1/100
        	$table->string('PSC', 16)->nullable(); // susPSC	Shipped Part Status Code	Part Status Code	PSC	N	String	1/16
        	$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('SUS_Segments');
    }
}
