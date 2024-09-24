<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReversalToNotificationPiecePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_piece_parts', function(Blueprint $table){
            $table->string('reversal_id')->nullable()->default(NULL)->after('rpsPDT');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_piece_parts', function(Blueprint $table){
            $table->dropColumn('deleted_at');
            $table->dropColumn('reversal_id');
        });
    }
}
