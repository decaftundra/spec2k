<?php

namespace App\Interfaces;

interface EID_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------------------------------------|
    | EID = Engine Information                                                                                                     |
    |------------------------------------------------------------------------------------------------------------------------------|
    | AET | Aircraft Engine Type                  | Aircraft Engine/APU Type                | Y   | String  | 1/20    | PW4000     |
	| EPC | Engine Position Code                  | Engine Position Identifier              | Y   | String  | 1/25    | 2          |
	| AEM | Aircraft Engine Model                 | Aircraft Engine/APU Model               | Y   | String  | 1/32    | PW4056     |
	| EMS | Engine Serial Number                  | Engine/APU Module Serial Number         | N   | String  | 1/20    | PCE-FA0006 |
	| MFR | Aircraft Engine Manufacturer Code     | Manufacturer Code                       | N   | String  | 5/5     | 77445      |
	| ETH | Engine Cumulative Hours               | Engine Cumulative Total Flight Hours    | N   | Decimal | 9,2     |            |
	| ETC | Engine Cumulative Cycles              | Engine Cumulative Total Cycles          | N   | Integer | 1/9     |            |
	|------------------------------------------------------------------------------------------------------------------------------|
	*/

	/**
     * Get the Aircraft Engine Type.
     *
     * @return string
     */
    public function get_EID_AET();


    /**
     * Summary of get_EID_AETO
     * LJMFeb23 MGTSUP-373 blank edits are breaking because the function doesnt exist so have a blank default.
     * @return string
     */
    public function get_EID_AETO();



    /**
     * Get the Engine Position Code.
     *
     * @return string
     */
    public function get_EID_EPC();

    /**
     * Get the Aircraft Engine Model.
     *
     * @return string
     */
    public function get_EID_AEM();


    /**
     * Summary of get_EID_AEMO
     * LJMFeb23 MGTSUP-373 blank edits are breaking because the function doesnt exist so have a blank default.
     * @return string
     */
    public function get_EID_AEMO();
    public function get_EID_LJMFILTERINFO();



    /**
     * Get the Engine Serial Number.
     *
     * @return string
     */
    public function get_EID_EMS();

    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_EID_MFR();

    /**
     * Get the Engine Cumulative Hours.
     *
     * @return float
     */
    public function get_EID_ETH();

    /**
     * Get the Engine Cumulative Cycles.
     *
     * @return integer
     */
    public function get_EID_ETC();
}