<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePowerBiPiecePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_bi_piece_parts', function (Blueprint $table) {
            $table->string('id')->index()->unique();
            
            $table->string('notification_id')->index();
            $table->foreign('notification_id')->references('id')->on('power_bi_shop_findings');
            
            $table->string('wpsSFI', 50)->index()->nullable();
            $table->string('wpsPPI')->index()->nullable();
            $table->string('wpsPFC', 1)->nullable();
            $table->string('wpsMFR', 5)->nullable();
            $table->string('wpsMFN', 55)->nullable();
            $table->string('wpsMPN', 32)->nullable();
            $table->string('wpsSER', 15)->nullable();
            $table->text('wpsFDE', 1000)->nullable();
            $table->string('wpsPNR', 15)->nullable();
            $table->string('wpsOPN', 32)->nullable();
            $table->string('wpsUSN', 20)->nullable();
            $table->string('wpsPDT', 100)->nullable();
            $table->string('wpsGEL', 30)->nullable();
            $table->dateTime('wpsMRD')->nullable();
            $table->string('wpsASN', 32)->nullable();
            $table->string('wpsUCN', 15)->nullable();
            $table->string('wpsSPL', 5)->nullable();
            $table->string('wpsUST', 20)->nullable();
            $table->boolean('is_wps_segment_valid')->nullable();
            
            $table->string('nhsMFR', 5)->nullable();
            $table->string('nhsMPN', 32)->nullable();
            $table->string('nhsSER', 15)->nullable();
            $table->string('nhsMFN', 55)->nullable();
            $table->string('nhsPNR', 15)->nullable();
            $table->string('nhsOPN', 32)->nullable();
            $table->string('nhsUSN', 20)->nullable();
            $table->string('nhsPDT', 100)->nullable();
            $table->string('nhsASN', 32)->nullable();
            $table->string('nhsUCN', 15)->nullable();
            $table->string('nhsSPL', 5)->nullable();
            $table->string('nhsUST', 20)->nullable();
            $table->string('nhsNPN', 32)->nullable();
            $table->boolean('is_nhs_segment_valid')->nullable();
            
            $table->string('rpsMPN', 32)->nullable();
            $table->string('rpsMFR', 5)->nullable(); 
            $table->string('rpsMFN', 55)->nullable();
            $table->string('rpsSER', 15)->nullable();
            $table->string('rpsPNR', 15)->nullable();
            $table->string('rpsOPN', 32)->nullable();
            $table->string('rpsUSN', 20)->nullable();
            $table->string('rpsASN', 32)->nullable();
            $table->string('rpsUCN', 15)->nullable();
            $table->string('rpsSPL', 5)->nullable();
            $table->string('rpsUST', 20)->nullable();
            $table->string('rpsPDT', 100)->nullable();
            $table->boolean('is_rps_segment_valid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('power_bi_piece_parts');
    }
}