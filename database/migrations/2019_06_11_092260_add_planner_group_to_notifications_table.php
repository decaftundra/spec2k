<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlannerGroupToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('notifications', function(Blueprint $table){
            $table->string('planner_group')->nullable()->after('sptMST');
            $table->foreign('planner_group')->references('planner_group')->on('users');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('notifications', function(Blueprint $table){
            $table->dropForeign('notifications_planner_group_foreign');
        });
        
        Schema::table('notifications', function(Blueprint $table){
            $table->dropColumn('planner_group');
        });
        
        Schema::enableForeignKeyConstraints();
    }
}