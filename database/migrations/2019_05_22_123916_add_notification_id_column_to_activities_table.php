<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationIdColumnToActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function(Blueprint $table){
            $table->string('shop_finding_id')->index()->nullable()->after('subject_type');
            $table->foreign('shop_finding_id')->references('id')->on('shop_findings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function(Blueprint $table){
            $table->dropForeign('activities_shop_finding_id_foreign');
            $table->dropColumn('shop_finding_id');
        });
    }
}