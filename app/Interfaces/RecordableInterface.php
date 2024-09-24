<?php

namespace App\Interfaces;

interface RecordableInterface
{
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl();
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle();
}