<?php

namespace App\Console\Commands;

use App\Events\SyncShopFindings as Sync;
use App\Notification;
use App\ShopFindings\ShopFinding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncShopFindings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:sync_shopfindings {shopfindingIds?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Matches all shop finding statuses, plant codes and planner groups with corresponding notification.';
    
    // Command file for Azure WebJobs Scheduler (plain text file saved with .cmd extension)
    // php %HOME%\site\artisan spec2kapp:sync_shopfindings

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
        $shopFindingIds = $this->argument('shopfindingIds');
        
        if ($shopFindingIds && count($shopFindingIds)) {
            
            Log::info('Number of Shop Finding Ids in sync command: ' . count($shopFindingIds));
            
            $total = 0;
            
            ShopFinding::withTrashed()->whereIn('id', $shopFindingIds)->chunk(100, function($shopFindings) use (&$total) {
                $total = $total + count($shopFindings);
                
                foreach ($shopFindings as $shopFinding) {
                    event(new Sync($shopFinding));
                }
            });
            
            Log::info('Total number of Shop Findings synced: ' . $total);
            
        } else {
            ShopFinding::withTrashed()->chunk(100, function($shopFindings){
                foreach ($shopFindings as $shopFinding) {
                    event(new Sync($shopFinding));
                }
            });
        }
    }
}
