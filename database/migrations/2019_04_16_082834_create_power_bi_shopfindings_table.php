<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePowerBiShopfindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_bi_shop_findings', function (Blueprint $table) {
            $table->string('id')->index()->unique();
            
            $table->string('status')->nullable();
            $table->dateTime('standby_at')->nullable();
            $table->dateTime('subcontracted_at')->nullable();
            $table->dateTime('scrapped_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            
            $table->boolean('is_valid')->default(0);
            $table->boolean('ready_to_export')->default(0);
            
            $table->string('hdrCHG', 1)->nullable();
            $table->string('hdrROC', 5)->nullable();
            $table->string('hdrOPR', 5)->nullable();
            $table->string('hdrRON', 55)->nullable();
            $table->string('hdrWHO', 55)->nullable();
            $table->boolean('is_hdr_segment_valid')->nullable();
            
            $table->string('aidMFR', 5)->nullable();
            $table->string('aidAMC', 20)->nullable();
            $table->string('aidMFN', 55)->nullable();
            $table->string('aidASE', 10)->nullable();
            $table->string('aidAIN', 10)->nullable();
            $table->string('aidREG', 10)->nullable();
            $table->string('aidOIN', 10)->nullable();
            $table->float('aidCTH', 9, 2)->nullable();
            $table->integer('aidCTY')->unsigned()->nullable();
            $table->boolean('is_aid_segment_valid')->nullable();
            
            $table->string('eidAET', 20)->nullable();
            $table->string('eidEPC', 25)->nullable();
            $table->string('eidAEM', 32)->nullable();
            $table->string('eidEMS', 20)->nullable();
            $table->string('eidMFR', 5)->nullable();
            $table->float('eidETH', 9, 2)->nullable();
            $table->integer('eidETC')->unsigned()->nullable();
            $table->boolean('is_eid_segment_valid')->nullable();
            
            $table->string('apiAET', 20)->nullable();
            $table->string('apiEMS', 20)->nullable();
            $table->string('apiAEM', 32)->nullable();
            $table->string('apiMFR', 5)->nullable();
            $table->float('apiATH', 9,2)->nullable();
            $table->integer('apiATC')->unsigned()->nullable();
            $table->boolean('is_api_segment_valid')->nullable();

            $table->string('rcsSFI', 50)->index()->nullable();
            $table->dateTime('rcsMRD')->nullable();
            $table->string('rcsMFR', 5)->nullable();
            $table->string('rcsMPN', 32)->nullable();
            $table->string('rcsSER', 15)->nullable();
            $table->string('rcsRRC', 1)->nullable();
            $table->string('rcsFFC', 2)->nullable();
            $table->string('rcsFFI', 2)->nullable();
            $table->string('rcsFCR', 2)->nullable();
            $table->string('rcsFAC', 2)->nullable();
            $table->string('rcsFBC', 2)->nullable();
            $table->string('rcsFHS', 2)->nullable();
            $table->string('rcsMFN', 55)->nullable();
            $table->string('rcsPNR', 15)->nullable();
            $table->string('rcsOPN', 32)->nullable();
            $table->string('rcsUSN', 35)->nullable();
            $table->string('rcsRET', 64)->nullable();
            $table->string('rcsCIC', 5)->nullable();
            $table->string('rcsCPO', 11)->nullable();
            $table->string('rcsPSN', 15)->nullable();
            $table->string('rcsWON', 20)->nullable();
            $table->string('rcsMRN', 32)->nullable();
            $table->string('rcsCTN', 15)->nullable();
            $table->string('rcsBOX', 10)->nullable();
            $table->string('rcsASN', 32)->nullable();
            $table->string('rcsUCN', 15)->nullable();
            $table->string('rcsSPL', 5)->nullable();
            $table->string('rcsUST', 20)->nullable();
            $table->string('rcsPDT', 100)->nullable();
            $table->string('rcsPML', 1000)->nullable();
            $table->string('rcsSFC', 10)->nullable();
            $table->string('rcsRSI', 50)->nullable();
            $table->string('rcsRLN', 25)->nullable();
            $table->text('rcsINT', 5000)->nullable();
            $table->text('rcsREM', 1000)->nullable();
            $table->boolean('is_rcs_segment_valid')->nullable();
            
            $table->text('sasINT', 5000)->nullable();
            $table->string('sasSHL', 3)->nullable();
            $table->boolean('sasRFI')->nullable();
            $table->string('sasMAT', 40)->nullable();
            $table->string('sasSAC', 5)->nullable();
            $table->boolean('sasSDI')->nullable();
            $table->string('sasPSC', 15)->nullable();
            $table->text('sasREM', 1000)->nullable();
            $table->boolean('is_sas_segment_valid')->nullable();
            
            $table->dateTime('susSHD')->nullable();
            $table->string('susMFR', 5)->nullable();
            $table->string('susMPN', 32)->nullable();
            $table->string('susSER', 30)->nullable();
            $table->string('susMFN', 55)->nullable();
            $table->string('susPDT', 100)->nullable();
            $table->string('susPNR', 32)->nullable();
            $table->string('susOPN', 32)->nullable();
            $table->string('susUSN', 35)->nullable();
            $table->string('susASN', 32)->nullable();
            $table->string('susUCN', 15)->nullable();
            $table->string('susSPL', 5)->nullable();
            $table->string('susUST', 20)->nullable();
            $table->string('susPML', 1000)->nullable();
            $table->string('susPSC', 16)->nullable();
            $table->boolean('is_sus_segment_valid')->nullable();
            
            $table->string('rlsMFR', 5)->nullable();
            $table->string('rlsMPN', 32)->nullable();
            $table->string('rlsSER', 30)->nullable();
            $table->dateTime('rlsRED')->nullable();
            $table->string('rlsTTY', 1)->nullable();
            $table->string('rlsRET', 64)->nullable();
            $table->dateTime('rlsDOI')->nullable();
            $table->string('rlsMFN', 55)->nullable();
            $table->string('rlsPNR', 32)->nullable();
            $table->string('rlsOPN', 32)->nullable();
            $table->string('rlsUSN', 35)->nullable();
            $table->text('rlsRMT', 5000)->nullable();
            $table->string('rlsAPT', 100)->nullable();
            $table->string('rlsCPI', 25)->nullable();
            $table->string('rlsCPT', 100)->nullable();
            $table->string('rlsPDT', 100)->nullable();
            $table->string('rlsPML', 1000)->nullable();
            $table->string('rlsASN', 32)->nullable();
            $table->string('rlsUCN', 15)->nullable();
            $table->string('rlsSPL', 5)->nullable();
            $table->string('rlsUST', 20)->nullable();
            $table->string('rlsRFR', 2)->nullable();
            $table->boolean('is_rls_segment_valid')->nullable();
            
            $table->string('lnkRTI', 50)->nullable();
            $table->boolean('is_lnk_segment_valid')->nullable();
            
            $table->string('attTRF', 1)->nullable();
            $table->integer('attOTT')->unsigned()->nullable();
            $table->integer('attOPC')->unsigned()->nullable();
            $table->integer('attODT')->unsigned()->nullable();
            $table->boolean('is_att_segment_valid')->nullable();
            
            $table->float('sptMAH', 8, 2)->nullable();
            $table->integer('sptFLW')->unsigned()->nullable();
            $table->integer('sptMST')->unsigned()->nullable();
            $table->boolean('is_spt_segment_valid')->nullable();
            
            $table->jsonb('values')->nullable();
            $table->boolean('is_misc_segment_valid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('power_bi_shop_findings');
    }
}