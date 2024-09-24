<?php

namespace App\Console\Commands;

use App\Location;
use App\Notification;
use Illuminate\Console\Command;

class AddPlantCodesToNotifications extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_plant_codes_to_notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the relevant plant codes to each notification without a plant code.';

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
        $notifications = Notification::whereNull('plant_code')->withTrashed()->get();
        
        $locations = Location::pluck('plant_code', 'sap_location_name');
        
        if (count($notifications)) {
            $count = 0;
            foreach ($notifications->chunk(100) as $chunk) {
                foreach ($chunk as $notification) {
                    if (isset($locations[$notification->hdrRON]) && !isset($notification->plant_code)) {
                        $this->info('Adding plant code ' . $locations[$notification->hdrRON] . ' to ID: ' . $notification->id);
                        $notification->plant_code = $locations[$notification->hdrRON];
                        $notification->save();
                        $count++;
                    }
                }
            }
            
            $this->info('Updated ' . $count . ' records.');
        }
    }
}
