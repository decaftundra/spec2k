<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSasSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SAS_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->text('INT', 5000)->nullable(); // sasINT	Shop Action Text Incoming	Inspection/Shop Action Text	INT	Y	String	1/5000
        	$table->string('SHL', 3); // sasSHL	Shop Repair Location Code	Shop Repair Facility Code	SHL	Y	String	1/3	R1
        	$table->boolean('RFI'); // sasRFI	Shop Final Action Indicator	Repair Final Action Indicator	RFI	Y	Boolean	1
        	$table->string('MAT', 40)->nullable(); // sasMAT	Mod (S) Incorporated (This Visit) Text	Manufacturer Authority Text	MAT	N	String	1/40
        	$table->string('SAC', 5)->nullable(); // sasSAC	Shop Action Code	Shop Action Code	SAC	N	String	1/5	RPLC
        	$table->boolean('SDI')->nullable()->default(NULL); // sasSDI	Shop Disclosure Indicator	Shop Disclosure Indicator	SDI	N	Boolean	0
        	$table->string('PSC', 15)->nullable(); // sasPSC	Part Status Code	Part Status Code	PSC	N	String	1/16	Overhauled
        	$table->text('REM', 1000)->nullable(); // sasREM	Comment Text	Remarks Text	REM	N	String	1/1000
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
        Schema::dropIfExists('SAS_Segments');
    }
}
