<?php

namespace App\Console\Commands;

use App\Segment;
use App\Activity;
use Illuminate\Console\Command;

class AddShopFindingIdsToActivities extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_shop_finding_ids_to_activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts shop finding id in activities where possible.';

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
        $activities = Activity::all();
        
        if (!empty($activities)) {
            foreach ($activities as $activity) {
                $activityClass = $activity->subject_type::find($activity->subject_id);
                
                if ($activityClass instanceof Segment) {
                    $activity->shop_finding_id = $activityClass->getShopFindingId();
                    $activity->save();
                }
            }
        }
    }
}
