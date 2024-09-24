<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToShopFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_findings', function ($table) {
            $table->string('status')->default('in_progress')->before('created_at');
            $table->dateTime('standby_at')->nullable()->before('created_at');
            $table->dateTime('subcontracted_at')->nullable()->before('created_at');
            $table->dateTime('scrapped_at')->nullable()->before('created_at');
            $table->dateTime('shipped_at')->nullable()->before('created_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_findings', function ($table) {
            $table->dropColumn('status');
            $table->dropColumn('standby_at');
            $table->dropColumn('subcontracted_at');
            $table->dropColumn('scrapped_at');
            $table->dropColumn('shipped_at');
            $table->dropSoftDeletes();
        });
    }
}