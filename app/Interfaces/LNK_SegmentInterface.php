<?php

namespace App\Interfaces;

interface LNK_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------|
    | LNK = Linking Fields                                                                           |
    |------------------------------------------------------------------------------------------------|
	| RTI | Removal Tracking Identifier     | Removal Tracking Identifier     | Y   | String  | 1/50 |
	|------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Removal Tracking Identifier.
     *
     * @return string
     */
    public function get_LNK_RTI();
}