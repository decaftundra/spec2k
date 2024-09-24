<?php

namespace App\Interfaces;

interface SAS_SegmentInterface
{
    /*
    |--------------------------------------------------------------------------------------------------------------------------------|
    | SAS = Shop Action Details                                                                                                      |
    |--------------------------------------------------------------------------------------------------------------------------------|
	| INT | Shop Action Text Incoming                   | Inspection/Shop Action Text     | Y   | String      | 1/5000  |            |
	| SHL | Shop Repair Location Code                   | Shop Repair Facility Code       | Y   | String      | 1/3     | R1         |
	| RFI | Shop Final Action Indicator                 | Repair Final Action Indicator   | Y   | Boolean     | 1       |            |
	| MAT | Mod (S) Incorporated (This Visit) Text      | Manufacturer Authority Text     | N   | String      | 1/40    |            |
	| SAC | Shop Action Code                            | Shop Action Code                | N   | String      | 1/5     | RPLC       |
	| SDI | Shop Disclosure Indicator                   | Shop Disclosure Indicator       | N   | Boolean     | 0       |            |
	| PSC | Part Status Code                            | Part Status Code                | N   | String      | 1/16    | Overhauled |
	| REM | Comment Text                                | Remarks Text                    | N   | String      | 1/1000  |            |
	|--------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Shop Action Text Incoming.
     *
     * @return string
     */
    public function get_SAS_INT();
    
    /**
     * Get the Shop Repair Location Code.
     *
     * @return string
     */
    public function get_SAS_SHL();
    
    /**
     * Get the Shop Final Action Indicator.
     *
     * @return boolean
     */
    public function get_SAS_RFI();
    
    /**
     * Get the Mod (S) Incorporated (This Visit) Text.
     *
     * @return string
     */
    public function get_SAS_MAT();
    
    /**
     * Get the Shop Action Code.
     *
     * @return string
     */
    public function get_SAS_SAC();
    
    /**
     * Get the Shop Disclosure Indicator.
     *
     * @return boolean
     */
    public function get_SAS_SDI();
    
    /**
     * Get the Part Status Code.
     *
     * @return string
     */
    public function get_SAS_PSC();
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_SAS_REM();
}