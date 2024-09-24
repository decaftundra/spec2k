<?php

namespace App\Console\Commands;

use App\Notification;
use Illuminate\Console\Command;
use App\ShopFindings\ShopFinding;

class AddPlantCodesToShopfindings extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_plant_codes_to_shopfindings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the relevant plant code to each shop finding.';

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
        $notifications = Notification::withTrashed()->whereNotNull('plant_code')->pluck('plant_code', 'id');
        
        $shopFindings = ShopFinding::withTrashed()->get();
        
        if (count($shopFindings) && count($notifications)) {
            foreach ($shopFindings->chunk(100) as $chunk) {
                foreach ($chunk as $shopFinding) {
                    if (isset($notifications[$shopFinding->id])) {
                        $shopFinding->plant_code = $notifications[$shopFinding->id];
                        $shopFinding->save();
                    }
                }
            }
        }
    }
}
