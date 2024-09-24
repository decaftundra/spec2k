<?php

namespace App\Listeners;

use App\Events\SegmentCreated;
use App\ShopFindings\ShopFinding;
use App\Traits\ValidationEventTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateSegmentValidation
{
    use ValidationEventTrait;
    
    /**
     * Handle the event.
     *
     * @param  SegmentCreated  $event
     * @return void
     */
    public function handle(SegmentCreated $event)
    {
        $event->segment->setIsValid('SegmentCreated');
        
        // Get the shop finding id.
        $shopFindingId = $this->getShopFindingId($event);
        
        $shopFinding = ShopFinding::find($shopFindingId);
        
        $shopFinding->setIsValid('SegmentCreated');
    }
}
