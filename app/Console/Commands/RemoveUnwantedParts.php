<?php

namespace App\Console\Commands;

use App\Location;
use App\PartList;
use Illuminate\Console\Command;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use App\Notification;

class RemoveUnwantedParts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:remove_unwanted_parts';
    
    // Command file for Azure WebJobs Scheduler (plain text file saved with .cmd extension)
    // php %HOME%\site\artisan spec2kapp:remove_unwanted_parts

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks each locations part list and removes parts from notifications and shop_findings tables accordingly.';
    
    public $locations;
    public $notificationIds = [];
    public $shopFindingIds = [];

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
        /*
        Set up.
        These don't work if they are in the constructor during tests for some reason.
        */
        
        $this->locations = Location::get();
        $this->setExcludedNotificationIds();
        $this->setExcludedShopFindingIds();
        
        // Set notification exclusions.
        Notification::whereIn('id', $this->notificationIds)->delete();
        
        // Set ShopFinding exclusions.
        ShopFinding::whereIn('id', $this->shopFindingIds)->delete();
    }
    
    /**
     * Set the excluded notification ids.
     *
     * @return void
     */
    private function setExcludedNotificationIds()
    {
        foreach ($this->locations as $location) {
            $partList = PartList::with('location')
                ->where('location_id', $location->id)
                ->first();
               
            if ($partList) {
                $filteredIds = $partList->getExcludedNotificationIds();
                
                $this->notificationIds = array_merge($this->notificationIds, $filteredIds);
            }
        }
    }
    
    /**
     * Set the excluded shop finding ids.
     *
     * @return void
     */
    private function setExcludedShopFindingIds()
    {
        foreach ($this->locations as $location) {
            $partList = PartList::with('location')
                ->where('location_id', $location->id)
                ->first();
                
            
                
            if ($partList) {
                $filteredIds = $partList->getExludedShopFindingIds();
                
                $this->shopFindingIds = array_merge($this->shopFindingIds, $filteredIds);
            }
        }
    }
}
