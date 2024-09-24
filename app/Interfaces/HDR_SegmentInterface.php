<?php

namespace App\Interfaces;

interface HDR_SegmentInterface
{
    /*
    |-------------------------------------------------------------------------------------------------------------------------|
    | HDR - Header                                                                                                            |
    |-------------------------------------------------------------------------------------------------------------------------|
    | CHG | Record Status                   | Change Code                     | Y   | String  | 1/1         | N               |
	| ROC | Reporting Organization Code     | Reporting Organization Code     | Y   | String  | 3/5         | 58960           |
	| RDT | Reporting Period Start Date     | Reporting Period Date           | Y   | Date    | 2001-07-01  |                 |
	| RSD | Reporting Period End Date       | Reporting Period End Date       | Y   | Date    | 2001-07-31  |                 |
	| OPR | Operator Code                   | Operator Code                   | Y   | String  | 3/5         | UAL             |
	| RON | Reporting Organization Name     | Reporting Organization Name     | N   | String  | 1/55        | Honeywell       |
	| WHO | Operator Name                   | Company Name                    | N   | String  | 1/55        | United Airlines |
	|-------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Change Code.
     *
     * @return string
     */
    public function get_HDR_CHG();
    
    /**
     * Get the Reporting Organization Code.
     *
     * @return string
     */
    public function get_HDR_ROC();
    
    /**
     * Get the Reporting Period Date Start.
     *
     * @return string
     */
    public function get_HDR_RDT();
    
    /**
     * Get the Reporting Period End Date.
     *
     * @return string
     */
    public function get_HDR_RSD();
    
    /**
     * Get the Operator Code.
     *
     * @return string
     */
    public function get_HDR_OPR();
    
    /**
     * Get the Reporting Organization Name.
     *
     * @return date
     */
    public function get_HDR_RON();
    
    /**
     * Get the Company Name.
     *
     * @return date
     */
    public function get_HDR_WHO();
}