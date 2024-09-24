<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCptColumnLengthOnRlsSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('RLS_Segments', function(Blueprint $table)
        {
            $table->string('CPT', 100)->nullable()->change(); // rlsCPT	Part Position	Component Position Text	CPT	N	String	1/100	Passenger door sect 15
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('RLS_Segments', function(Blueprint $table)
        {
            $table->string('CPT', 100)->nullable()->change(); // rlsCPT	Part Position	Component Position Text	CPT	N	String	1/100	Passenger door sect 15
        });
    }
}
