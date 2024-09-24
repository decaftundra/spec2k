<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RPS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('piece_part_detail_id')->unsigned();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
            $table->string('MPN', 32); // rpsMPN	Replaced Piece Part Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	Y	String	1/32
        	$table->string('MFR', 5)->nullable(); // rpsMFR	Replaced Piece Part Vendor Code	Manufacturer Code	MFR	N	String	5/5
        	$table->string('MFN', 55)->nullable(); // rpsMFN	Replaced Piece Part Vendor Name	Manufacturer Name	MFN	N	String	1/55
        	$table->string('SER', 15)->nullable(); // rpsSER	Replaced Vendor Piece Part Serial Number	Part Serial Number	SER	N	String	1/15
        	$table->string('PNR', 15)->nullable(); // rpsPNR	Replaced Vendor Piece Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // rpsOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // rpsUSN	Replaced Piece Part Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('ASN', 32)->nullable(); // rpsASN	Replaced Operator Piece Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // rpsUCN	Replaced Operator Piece Part Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // rpsSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // rpsUST	Replaced Piece Part Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
        	$table->string('PDT', 100)->nullable(); // rpsPDT	Replaced Vendor Piece Part Description	Part Description	PDT	N	String	1/100
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
        Schema::dropIfExists('RPS_Segments');
    }
}
