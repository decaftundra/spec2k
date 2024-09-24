<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopFindingsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_findings_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_finding_id')->unsigned();
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
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
        Schema::dropIfExists('shop_findings_details');
    }
}
