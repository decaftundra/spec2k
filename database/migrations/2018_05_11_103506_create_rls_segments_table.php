<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRlsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RLS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('MFR', 5); // rlsMFR	Removed Part Manufacturer Code	Manufacturer Code	MFR	Y	String	5/5
        	$table->string('MPN', 32); // rlsMPN	Removed Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	Y	String	1/32
        	$table->string('SER', 15); // rlsSER	Removed Manufacturer Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->dateTime('RED')->nullable()->default(NULL); // rlsRED	Removal Date	Part Removal Date	RED	N	Date	YYYY-MM-DD
        	$table->string('TTY', 1)->nullable(); // rlsTTY	Removal Type Code	Removal Type Code	TTY	N	String	1/1	S
        	$table->string('RET', 64)->nullable(); // rlsRET	Removal Type Text	Reason for Removal Clarification Text	RET	N	String	1/64
        	$table->dateTime('DOI')->nullable()->default(NULL); // rlsDOI	Install Date of	Removed Part	Installation Date	DOI	N	Date	2001-06-01
        	$table->string('MFN', 55)->nullable(); // rlsMFN	Removed Part Manufacturer Name	Manufacturer Name	MFN	N	String	1/55	Honeywell
        	$table->string('PNR', 15)->nullable(); // rlsPNR	Removed Manufacturer Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // rlsOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // rlsUSN	Removed Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->text('RMT', 5000)->nullable(); // rlsRMT	Removal Reason Text	Removal Reason Text	RMT	N	String	1/5000
        	$table->string('APT', 100)->nullable(); // rlsAPT	Engine/APU Position Identifier	Aircraft Engine/APU Position Text	APT	N	String	1/100
        	$table->string('CPI', 25)->nullable(); // rlsCPI	Part Position Code	Component Position Code	CPI	N	String	1/25	LB061
        	$table->string('CPT', 100)->nullable(); // rlsCPT	Part Position	Component Position Text	CPT	N	String	1/100	Passenger door sect 15
        	$table->string('PDT', 100)->nullable(); // rlsPDT	Removed Part Description	Part Description	PDT	N	String	1/100
        	$table->string('PML', 100)->nullable(); // rlsPML	Removed Part Modification Level	Part Modification Level	PML	N	String	1/100
        	$table->string('ASN', 32)->nullable(); // rlsASN	Removed Operator Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // rlsUCN	Removed Operator Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // rlsSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // rlsUST	Removed Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
        	$table->string('RFR', 2)->nullable(); // rlsRFR	Removal Reason Code	Reason for Removal Code	RFR	N	String	2/2
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
        Schema::dropIfExists('RLS_Segments');
    }
}
