<?php

namespace App\Listeners;

use App\Activity;
use Carbon\Carbon;
use App\Events\PiecePartsBatchUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PiecePartBatchActivitiesUpdate
{
    /**
     * Handle the event.
     *
     * @param  PiecePartsBatchUpdated  $event
     * @return void
     */
    public function handle(PiecePartsBatchUpdated $event)
    {
        //Log::info('PiecePartsBatchUpdated fired');
        //Log::info($event->segment);
        //Log::info($event->shopFindingId);
        //Log::info($event->userId);
        //Log::info($event->segmentIds);
        
        $activities = [];
        $now = Carbon::now();
        $i = 0;
        
        if (count($event->segmentIds)) {
            foreach ($event->segmentIds as $segmentId) {
                $activities[$i]['subject_id'] = $segmentId;
                $activities[$i]['subject_type'] = $event->segment;
                $activities[$i]['shop_finding_id'] = $event->shopFindingId;
                $activities[$i]['name'] = 'updated';
                $activities[$i]['user_id'] = $event->userId;
                $activities[$i]['created_at'] = $now;
                $activities[$i]['updated_at'] = $now;
                $i++;
            }
            
            Activity::insert($activities);
        }
    }
}
