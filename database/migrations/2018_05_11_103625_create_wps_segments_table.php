<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWpsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('WPS_Segments', function (Blueprint $table) {
            
            $driver = Schema::connection($this->getConnection())->getConnection()->getDriverName();
            
            $table->integer('piece_part_detail_id')->unsigned();
            $table->foreign('piece_part_detail_id')->references('id')->on('piece_part_details');
            $table->string('SFI', 50); // wpsSFI	Shop Finding Record Identifier	Shop Findings Record Identifier	SFI	Y	String	1/50
            
            if ($driver === 'sqlite') {
                $table->increments('id');
                $table->string('PPI')->unique()->index();
            } else {
                $table->increments('PPI');
            }
            
        	// Had to make this nullable even though it needs a value.
        	$table->string('PFC', 1)->nullable(); // wpsPFC	Primary Piece Part Failure Indicator	Primary Piece Part Failure Indicator	PFC	Y	String	1/1	Y
        	$table->string('MFR', 5)->nullable(); // wpsMFR	Failed Piece Part Vendor Code	Manufacturer Code	MFR	N	String	5/5
        	$table->string('MFN', 55)->nullable(); // wpsMFN	Failed Piece Part Vendor Name	Manufacturer Name	MFN	N	String	1/55
        	$table->string('MPN', 32)->nullable(); // wpsMPN	Failed Piece Part Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	N	String	1/32
        	$table->string('SER', 15)->nullable(); // wpsSER	Failed Piece Part Serial Number	Part Serial Number	SER	N	String	1/15
        	$table->text('FDE', 1000)->nullable(); // wpsFDE	Piece Part Failure Description	Piece Part Failure Description	FDE	N	String	1/1000
        	$table->string('PNR', 15)->nullable(); // wpsPNR	Vendor Piece Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // wpsOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // wpsUSN	Piece Part Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('PDT', 100)->nullable(); // wpsPDT	Failed Piece Part Description	Part Description	PDT	N	String	1/100
        	$table->string('GEL', 30)->nullable(); // wpsGEL	Piece Part Reference Designator Symbol	Geographic and/or Electrical Location	GEL	N	String	1/30
        	$table->dateTime('MRD')->nullable()->default(NULL); // wpsMRD	Received Date	Material Receipt Date	MRD	N	Date	YYYY-MM-DD
        	$table->string('ASN', 32)->nullable(); // wpsASN	Operator Piece Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // wpsUCN	Operator Piece Part Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // wpsSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // wpsUST	Piece Part Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
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
        Schema::dropIfExists('WPS_Segments');
    }
}
