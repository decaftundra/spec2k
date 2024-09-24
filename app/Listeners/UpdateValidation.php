<?php

namespace App\Listeners;

use App\ShopFindings\ShopFinding;
use App\Events\SegmentDeleted;
use App\Traits\ValidationEventTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateValidation
{
    use ValidationEventTrait;
    
    /**
     * Handle the event.
     *
     * @param  SegmentDeleted  $event
     * @return void
     */
    public function handle(SegmentDeleted $event)
    {
        // Get the shop finding id.
        $shopFindingId = $this->getShopFindingId($event);
        
        $shopFinding = ShopFinding::withTrashed()->find($shopFindingId);
        
        $shopFinding->setIsValid('SegmentDeleted');
    }
}
