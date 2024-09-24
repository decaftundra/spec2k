<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSptSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SPT_Segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_findings_detail_id')->unsigned();
            $table->foreign('shop_findings_detail_id')->references('id')->on('shop_findings_details');
            $table->float('MAH', 8, 2)->nullable(); // sptMAH	Shop Total Labor Hours	Total Labor Hours	MAH	N	Decimal	8,2	110.00
        	$table->integer('FLW')->unsigned()->nullable(); // sptFLW	Shop Flow Time	Shop Flow Time	FLW	N	Integer	1/9
        	$table->integer('MST')->unsigned()->nullable(); // sptMST	Shop Turn Around Time	Mean Shop Processing Time	MST	N	Integer	1/4
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
        Schema::dropIfExists('SPT_Segments');
    }
}
