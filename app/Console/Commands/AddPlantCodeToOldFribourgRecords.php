<?php

namespace App\Console\Commands;

use App\Notification;
use App\ShopFindings\ShopFinding;
use Illuminate\Console\Command;

class AddPlantCodeToOldFribourgRecords extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_plant_codes_to_old_fribourg_records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds plant codes to old Fribourg notifications and shop finding records.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifications = Notification::whereNull('plant_code')->where('hdrROC', 'S3960')->get();
        
        if (count($notifications)) {
            foreach ($notifications as $notification) {
                $notification->plant_code = 2200;
                $notification->save();
            }
        }
        
        $shopFindings = ShopFinding::whereNull('plant_code')->whereHas('HDR_Segment', function($q){
            $q->where('ROC', 'S3960');
        })->get();
        
        if (count($shopFindings)) {
            foreach ($shopFindings as $shopFinding) {
                $shopFinding->plant_code = 2200;
                $shopFinding->save();
            }
        }
    }
}
