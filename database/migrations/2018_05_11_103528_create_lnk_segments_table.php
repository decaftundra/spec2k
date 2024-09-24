<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLnkSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('LNK_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->string('RTI', 50); // lnkRTI	Removal Tracking Identifier	Removal Tracking Identifier	RTI	Y	String	1/50
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
        Schema::dropIfExists('LNK_Segments');
    }
}
