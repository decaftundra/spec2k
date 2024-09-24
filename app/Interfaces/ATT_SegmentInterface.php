<?php

namespace App\Interfaces;

interface ATT_SegmentInterface
{
    /*
    |---------------------------------------------------------------------------------------------------|
    | ATT = Accumulated Time Text (Removed LRU)                                                         |
    |---------------------------------------------------------------------------------------------------|
	| TRF | Time/Cycle Reference Code       | Time/Cycle Reference Code       | Y   | String      | 1/1 |
	| OTT | Operating Time                  | Operating Time                  | N   | Integer     | 1/6 |
	| OPC | Operating Cycle Count           | Operating Cycle Count           | N   | Integer     | 1/6 |
	| ODT | Operating Day Count             | Operating Days                  | N   | Integer     | 1/6 |
	|---------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Removal Tracking Identifier.
     *
     * @return string
     */
    public function get_ATT_TRF();
    
    /**
     * Get the Operating Time.
     *
     * @return integer
     */
    public function get_ATT_OTT();
    
    /**
     * Get the Operating Cycle Count.
     *
     * @return integer
     */
    public function get_ATT_OPC();
    
    /**
     * Get the Operating Day Count.
     *
     * @return integer
     */
    public function get_ATT_ODT();
}