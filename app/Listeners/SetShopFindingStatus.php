<?php

namespace App\Listeners;

use Log;
use Illuminate\Queue\InteractsWithQueue;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetShopFindingStatus //implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  (mixed)  $event
     * @return void
     */
    public function handle($event)
    {
        //Log::debug('SetShopFindingStatus listener fired.');
        
        if ($event->shopFinding) {
            $id = $event->shopFinding->id;
        
            if (isset($event->notification)) {
                //Log::debug('Found updating notification.', [$event->notification]);
                
                $notification = $event->notification;
            } else {

                //Log::debug('Getting stored notification.');
                
                $notification = Notification::withTrashed()->find((string) $id);
            }
            
            if ($notification) {
                $status = $notification->status;
                $statusDate = $notification->getStatusDate();
            } else {
                $status = 'in_progress';
                $statusDate = date('Y-m-d H:i:s');
            }
            
            $event->shopFinding->setStatus($status, $statusDate);
            $event->shopFinding->setIsValid('SetShopFindingStatus');
        }
    }
}
