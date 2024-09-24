<?php

namespace App\Listeners;

use Log;
use App\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetShopFindingPlantCode
{
    /**
     * Handle the event.
     *
     * @param  SyncShopFindings  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->shopFinding) {
            $id = $event->shopFinding->id;
        
            if (isset($event->notification)) {
                $notification = $event->notification;
            } else {
                $notification = Notification::withTrashed()->find((string) $id);
            }
            
            if ($notification) {
                $plantCode = $notification->plant_code;
                $event->shopFinding->plant_code = $plantCode;
                $event->shopFinding->save();
            }
        }
    }
}
