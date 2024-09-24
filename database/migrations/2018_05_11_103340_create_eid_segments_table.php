<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEidSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EID_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('AET', 20); // eidAET	Aircraft Engine Type	Aircraft Engine/APU Type	AET	Y	String	1/20	PW4000
        	$table->string('EPC', 25); // eidEPC	Engine Position Code	Engine Position Identifier	EPC	Y	String	1/25	2
        	$table->string('AEM', 32); // eidAEM	Aircraft Engine Model	Aircraft Engine/APU Model	AEM	Y	String	1/32	PW4056
        	$table->string('EMS', 20)->nullable(); // eidEMS	Engine Serial Number	Engine/APU Module Serial Number	EMS	N	String	1/20	PCE-FA0006
        	$table->string('MFR', 5)->nullable(); // eidMFR	Aircraft Engine Manufacturer Code	Manufacturer Code	MFR	N	String	5/5	77445
        	$table->float('ETH', 9, 2)->nullable(); // eidETH	Engine Cumulative Hours	Engine Cumulative Total Flight Hours	ETH	N	Decimal	9,2
        	$table->integer('ETC')->unsigned()->nullable(); // eidETC	Engine Cumulative Cycles	Engine Cumulative Total Cycles	ETC	N	Integer	1/9
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
        Schema::dropIfExists('EID_Segments');
    }
}
