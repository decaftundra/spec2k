<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNhsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NHS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('piece_part_detail_id')->unsigned();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
            $table->string('MFR', 5); // nhsMFR	Failed Piece Part Next Higher Assembly Part Manufacturer Code	Manufacturer Code	MFR	Y	String	5/5
        	$table->string('MPN', 32); // nhsMPN	Next Higher Assembly Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	Y	String	1/32
        	$table->string('SER', 15); // nhsSER	Failed Piece Part Next Higher Assembly Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->string('MFN', 55)->nullable(); // nhsMFN	Failed Piece Part Next Higher Assembly Part Manufacturer Name	Manufacturer Name	MFN	N	String	1/55
        	$table->string('PNR', 15)->nullable(); // nhsPNR	Failed Piece Part Next Higher Assembly Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // nhsOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // nhsUSN	Failed Piece Part Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('PDT', 100)->nullable(); // nhsPDT	Failed Piece Part Next Higher Assembly Part Name	Part Description	PDT	N	String	1/100
        	$table->string('ASN', 32)->nullable(); // nhsASN	Failed Piece Part Next Higher Assembly Operator Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // nhsUCN	Failed Piece Part Next Higher Assembly Operator Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // nhsSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // nhsUST	Failed Piece Part NHA Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
        	$table->string('NPN', 32)->nullable(); // nhsNPN	Failed Piece Part Next Higher Assembly NHA Part Number	Failed Piece Part Next Higher Assembly NHA Part Number	NPN	N	String	1/32
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
        Schema::dropIfExists('NHS_Segments');
    }
}
