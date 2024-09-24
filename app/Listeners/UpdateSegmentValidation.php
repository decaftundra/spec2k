<?php

namespace App\Listeners;

use App\Events\SegmentUpdated;
use App\ShopFindings\ShopFinding;
use App\Traits\ValidationEventTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSegmentValidation
{
    use ValidationEventTrait;
    
    /**
     * Handle the event.
     *
     * @param  SegmentCreated  $event
     * @return void
     */
    public function handle(SegmentUpdated $event)
    {
        $event->segment->setIsValid('SegmentUpdated');
        
        // Get the shop finding id.
        $shopFindingId = $this->getShopFindingId($event);
        
        $shopFinding = ShopFinding::withTrashed()->find($shopFindingId);
        
        $shopFinding->setIsValid('SegmentUpdated');
    }
}
