<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ATT_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('TRF', 1); // attTRF	Time/Cycle Reference Code	Time/Cycle Reference Code	TRF	Y	String	1/1
        	$table->integer('OTT')->unsigned()->nullable(); // attOTT	Operating Time	Operating Time	OTT	N	Integer	1/6
        	$table->integer('OPC')->unsigned()->nullable(); // attOPC	Operating Cycle Count	Operating Cycle Count	OPC	N	Integer	1/6
        	$table->integer('ODT')->unsigned()->nullable(); // attODT	Operating Day Count	Operating Days	ODT	N	Integer	1/6
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
        Schema::dropIfExists('ATT_Segments');
    }
}
