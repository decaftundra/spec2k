<?php

namespace App\Interfaces;

interface SegmentInterface
{
    /**
     * Get an array of all segment attributes.
     *
     * @return array
     */
    public function getAttributes();
    
    /**
     * Get the segment attribute keys that should be treated as dates.
     *
     * @return array
     */
    public function getDates();
}