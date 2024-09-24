<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RCS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('SFI', 50)->unique(); // rcsSFI	Shop Findings Record Identifier	Shop Findings Record Identifier	SFI	Y	String	1/50
        	$table->dateTime('MRD'); // rcsMRD	Shop Received Date	Material Receipt	Date	MRD	Y	Date	YYYY-MM-DD
        	$table->string('MFR', 5); // rcsMFR	Received Part Manufacturer Code	Manufacturer Code	MFR	Y	String	5/5
        	$table->string('MPN', 32); // rcsMPN	Received Manufacturer Full Length Part Number	Manufacturer Full Length Part Number	MPN	Y	String	1/32
        	$table->string('SER', 15); // rcsSER	Received Manufacturer Serial Number	Part Serial Number	SER	Y	String	1/15
        	$table->string('RRC', 1); // rcsRRC	Supplier Removal Type Code	Supplier Removal Type Code	RRC	Y	String	1/1	S
        	$table->string('FFC', 2); // rcsFFC	Failure/ Fault Found	Failure/Fault Found Code	FFC	Y	String	1/2	FT
        	$table->string('FFI', 2); // rcsFFI	Failure/ Fault Induced	Failure/Fault Induced Code	FFI	Y	String	1/2	NI
        	$table->string('FCR', 2); // rcsFCR	Failure/ Fault Confirms Reason For Removal	Failure/Fault Confirm Reason Code	FCR	Y	String	1/2	CR
        	$table->string('FAC', 2); // rcsFAC	Failure/ Fault Confirms Aircraft Message	Failure/Fault Confirm Aircraft Message Code	FAC	Y	String	1/2	NA
        	$table->string('FBC', 2); // rcsFBC	Failure/ Fault Confirms Aircraft Part Bite Message	Failure/Fault Confirm Bite Message Code	FBC	Y	String	1/2	NB
        	$table->string('FHS', 2); // rcsFHS	Hardware/Software Failure	Hardware/Software Failure Code	FHS	Y	String	1/2	SW
        	$table->string('MFN', 55)->nullable(); // rcsMFN	Removed Part Manufacturer Name	Manufacturer Name	MFN	N	String	1/55	Honeywell
        	$table->string('PNR', 15)->nullable(); // rcsPNR	Received Manufacturer Part Number	Part Number	PNR	N	String	1/15
        	$table->string('OPN', 32)->nullable(); // rcsOPN	Overlength Part Number	Overlength Part Number	OPN	N	String	16/32
        	$table->string('USN', 20)->nullable(); // rcsUSN	Removed Universal Serial Number	Universal Serial Number	USN	N	String	6/20
        	$table->string('RET', 64)->nullable(); // rcsRET	Supplier Removal Type Text	Reason for Removal Clarification Text	RET	N	String	1/64
        	$table->string('CIC', 5)->nullable(); // rcsCIC	Customer Code Customer	Identification Code	CIC	N	String	3/5	UAL
        	$table->string('CPO', 11)->nullable(); // rcsCPO	Repair Order Identifier	Customer Order Number	CPO	N	String	1/11	123UA13
        	$table->string('PSN', 15)->nullable(); // rcsPSN	Packing Sheet Number	Packing Sheet Number	PSN	N	String	1/15	123UA13PS1
        	$table->string('WON', 20)->nullable(); // rcsWON	Work Order Number	Work Order Number	WON	N	String	1/20	123UA13WO1
        	$table->string('MRN', 32)->nullable(); // rcsMRN	Maintenance Release Authorization Number	Maintenance Release Authorization Number	MRN	N	String	1/32
        	$table->string('CTN', 15)->nullable(); // rcsCTN	Contract Number	Contract Number	CTN	N	String	4/15	123UA13CT1
        	$table->string('BOX', 10)->nullable(); // rcsBOX	Master Carton Number	Master Carton Number	BOX	N	String	1/10	123UA13BX1
        	$table->string('ASN', 32)->nullable(); // rcsASN	Received Operator Part Number	Airline Stock Number	ASN	N	String	1/32
        	$table->string('UCN', 15)->nullable(); // rcsUCN	Received Operator Serial Number	Unique Component Identification Number	UCN	N	String	1/15
        	$table->string('SPL', 5)->nullable(); // rcsSPL	Supplier Code	Supplier Code	SPL	N	String	5/5
        	$table->string('UST', 20)->nullable(); // rcsUST	Removed Universal Serial Tracking Number	Universal Serial Tracking Number	UST	N	String	6/20
        	$table->string('PDT', 100)->nullable(); // rcsPDT	Manufacturer Part Description	Part Description	PDT	N	String	1/100
        	$table->string('PML', 100)->nullable(); // rcsPML	Removed Part Modificiation Level	Part Modification Level	PML	N	String	1/100
        	$table->string('SFC', 10)->nullable(); // rcsSFC	Shop Findings Code	Shop Findings Code	SFC	N	String	1/10
        	$table->string('RSI', 50)->nullable(); // rcsRSI	Related Shop Finding Record Identifier	Related Shop Findings Record Identifier	RSI	N	String	1/50
        	$table->string('RLN', 25)->nullable(); // rcsRLN	Repair Location Name	Repair Location Name	RLN	N	String	1/25
        	$table->text('INT', 5000)->nullable(); // rcsINT	Incoming Inspection Text	Incoming Inspection/Shop Action Text	INT	N	String	1/5000
        	$table->text('REM', 1000)->nullable(); // rcsREM	Comment Text	Remarks Text	REM	N	String	1/1000
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
        Schema::dropIfExists('RCS_Segments');
    }
}
