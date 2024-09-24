<?php

namespace App\Traits;

trait ValidationEventTrait
{
    /**
     * Get the shop finding id related to the segment.
     *
     * @param App\Events\SegmentSaving $event
     * @return integer
     */
    protected function getShopFindingId($event)
    {
        $class = get_class($event->segment);
        
        if (is_subclass_of($class, \App\PieceParts\PiecePartSegment::class)) {
            return $event->segment->getShopFindingId();
        }
        
        return $event->segment->getIdentifier();
    }
}