<?php

namespace App\Listeners;

use Log;
use Illuminate\Queue\InteractsWithQueue;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetShopFindingPlannerGroup
{
    /**
     * Handle the event.
     *
     * @param  ShopFindingCreated  $event
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
                $plannerGroup = $notification->planner_group;
                $event->shopFinding->planner_group = $plannerGroup;
                $event->shopFinding->save();
            }
        }
    }
}
