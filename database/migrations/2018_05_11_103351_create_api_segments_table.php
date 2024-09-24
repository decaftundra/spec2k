<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('API_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('AET', 20); // apiAET	Aircraft APU Type	Aircraft Engine/APU Type	AET	Y	String	1/20	331-400B
        	$table->string('EMS', 20); // apiEMS	APU Serial Number	Engine/APU Module Serial Number	EMS	Y	String	1/20	SP-E994180
        	$table->string('AEM', 32)->nullable(); // apiAEM	Aircraft APU Model	Aircraft Engine/APU Model	AEM	N	String	1/32	3800608-2
        	$table->string('MFR', 5)->nullable(); // apiMFR	Aircraft Engine Manufacturer Code	Manufacturer Code	MFR	N	String	5/5	99193
        	$table->float('ATH', 9,2)->nullable(); // apiATH	APU Cumulative Hours	APU Cumulative Total Hours	ATH	N	Decimal	9,2
        	$table->integer('ATC')->unsigned()->nullable(); // apiATC	APU Cumulative Cycles	APU Cumulative Total Cycles	ATC	N	Integer	1/9
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
        Schema::dropIfExists('API_Segments');
    }
}
