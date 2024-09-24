<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHdrSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('HDR_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_finding_id')->unsigned();
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
            $table->integer('piece_part_id')->unsigned()->nullable();
            $table->foreign('piece_part_id')->references('id')->on('piece_parts');
            $table->string('CHG', 1); // Record Status	Change Code CHG Y String 1/1 N
            $table->string('ROC', 5); // Reporting Organization Code	Reporting Organization Code	ROC	Y	String	3/5	58960
            $table->dateTime('RDT'); // Reporting Period Start Date	Reporting Period	Date	RDT	Y	Date	2001-07-01
            $table->dateTime('RSD'); // Reporting Period End Date	Reporting Period End Date	RSD	Y	Date	2001-07-31
            $table->string('OPR', 5); // Operator Code	Operator Code	OPR	Y	String	3/5	UAL
            $table->string('RON', 55)->nullable(); // Reporting Organization Name	Reporting Organization Name	RON	N	String	1/55	Honeywell
            $table->string('WHO', 55)->nullable(); // Operator Name	Company Name	WHO	N	String	1/55	United Airlines
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
        Schema::dropIfExists('HDR_Segments');
    }
}
