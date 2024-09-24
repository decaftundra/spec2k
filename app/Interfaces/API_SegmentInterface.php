<?php

namespace App\Interfaces;

interface API_SegmentInterface
{
    /*
    |--------------------------------------------------------------------------------------------------------------------------------|
    | API = APU Information                                                                                                          |
    |--------------------------------------------------------------------------------------------------------------------------------|
    | AET | Aircraft APU Type                       | Aircraft Engine/APU Type            | Y   | String      | 1/20    | 331-400B   |
	| EMS | APU Serial Number                       | Engine/APU Module Serial Number     | Y   | String      | 1/20    | SP-E994180 |
	| AEM | Aircraft APU Model                      | Aircraft Engine/APU Model           | N   | String      | 1/32    | 3800608-2  |
	| MFR | Aircraft Engine Manufacturer Code       | Manufacturer Code                   | N   | String      | 5/5     | 99193      |
	| ATH | APU Cumulative Hours                    | APU Cumulative Total Hours          | N   | Decimal     | 9,2     |            |
	| ATC | APU Cumulative Cycles                   | APU Cumulative Total Cycles         | N   | Integer     | 1/9     |            |
	|--------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Aircraft APU Type.
     *
     * @return String
     */
    public function get_API_AET();
    
    /**
     * Get the APU Serial Number.
     *
     * @return String
     */
    public function get_API_EMS();
    
    /**
     * Get the Aircraft APU Model.
     *
     * @return String
     */
    public function get_API_AEM();
    
    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return String
     */
    public function get_API_MFR();
    
    /**
     * Get the APU Cumulative Hours.
     *
     * @return float
     */
    public function get_API_ATH();
    
    /**
     * Get the APU Cumulative Cycles.
     *
     * @return Integer
     */
    public function get_API_ATC();
}