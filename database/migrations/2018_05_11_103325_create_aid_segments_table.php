<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAidSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('AID_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('MFR', 5); // aidMFR	Airframe Manufacturer Code	Manufacturer Code	MFR	Y	String	5/5	S4956
        	$table->string('AMC', 20); // aidAMC	Aircraft Model	Aircraft Model Identifier	AMC	Y	String	1/20	757
        	$table->string('MFN', 55)->nullable(); // aidMFN	Airframe Manufacturer Name	Manufacturer Name	MFN	N	String	1/55	EMBRAER
        	$table->string('ASE', 10)->nullable(); // aidASE	Aircraft Series	Aircraft Series Identifier	ASE	N	String	3/10	300F
        	$table->string('AIN', 10)->nullable(); // aidAIN	Aircraft Manufacturer Serial Number	Aircraft Identification Number	AIN	N	String	1/10	25398
        	$table->string('REG', 10)->nullable(); // aidREG	Aircraft Registration Number	Aircraft Fully Qualified Registration Number	REG	N	String	1/10
        	$table->string('OIN', 10)->nullable(); // aidOIN	Operator Aircraft Internal Identifier	Operator Aircraft Internal Identifier	OIN	N	String	1/10
        	$table->float('CTH', 9, 2)->nullable(); // aidCTH	Aircraft Cumulative Total Flight Hours	Aircraft Cumulative Total Flight Hours	CTH	N	Decimal	9,2
        	$table->integer('CTY')->unsigned()->nullable(); // aidCTY	Aircraft Cumulative Total Cycles	Aircraft Cumulative Total Cycles	CTY	N	Integer	1/9
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
        Schema::dropIfExists('AID_Segments');
    }
}
