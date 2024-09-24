<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_findings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ataID')->nullable(); // author id
            $table->integer('ataVersion')->unsigned()->default(1);
            $table->integer('SFVersion')->unsigned()->default(1);
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
        Schema::dropIfExists('shop_findings');
    }
}
