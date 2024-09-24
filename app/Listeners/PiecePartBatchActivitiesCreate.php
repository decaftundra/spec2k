<?php

namespace App\Listeners;

use App\Activity;
use Carbon\Carbon;
use App\Events\PiecePartsBatchCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PiecePartBatchActivitiesCreate
{
    /**
     * Handle the event.
     *
     * @param  PiecePartsBatchCreated  $event
     * @return void
     */
    public function handle(PiecePartsBatchCreated $event)
    {
        $activities = [];
        $now = Carbon::now();
        $i = 0;
        
        if (count($event->segmentIds)) {
            foreach ($event->segmentIds as $segmentId) {
                $activities[$i]['subject_id'] = $segmentId;
                $activities[$i]['subject_type'] = $event->segment;
                $activities[$i]['shop_finding_id'] = $event->shopFindingId;
                $activities[$i]['name'] = 'created';
                $activities[$i]['user_id'] = $event->userId;
                $activities[$i]['created_at'] = $now;
                $activities[$i]['updated_at'] = $now;
                $i++;
            }
            
            Activity::insert($activities);
        }
    }
}
