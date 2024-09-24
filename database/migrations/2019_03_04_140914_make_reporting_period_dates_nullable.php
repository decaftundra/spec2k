<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeReportingPeriodDatesNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('HDR_Segments', function(Blueprint $table)
        {
            $table->dateTime('RDT')->nullable()->change();
            $table->dateTime('RSD')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('HDR_Segments', function(Blueprint $table)
        {
            $table->dateTime('RDT')->change();
            $table->dateTime('RSD')->change();
        });
    }
}
