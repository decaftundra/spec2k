<?php

namespace App\Interfaces;

interface AID_SegmentInterface
{
    /*
    |----------------------------------------------------------------------------------------------------------------------------------------|
    | AID - Airframe Information                                                                                                             |
    |----------------------------------------------------------------------------------------------------------------------------------------|
    | MFR | Airframe Manufacturer Code              | Manufacturer Code                             | Y    | String      | 5/5     | S4956   |
	| AMC | Aircraft Model                          | Aircraft Model Identifier                     | Y    | String      | 1/20    | 757     |
	| MFN | Airframe Manufacturer Name              | Manufacturer Name                             | N    | String      | 1/55    | EMBRAER |
	| ASE | Aircraft Series                         | Aircraft Series Identifier                    | N    | String      | 3/10    | 300F    |
	| AIN | Aircraft Manufacturer Serial Number     | Aircraft Identification Number                | N    | String      | 1/10    | 25398   |
	| REG | Aircraft Registration Number            | Aircraft Fully Qualified Registration Number  | N    | String      | 1/10    |         |
	| OIN | Operator Aircraft Internal Identifier   | Operator Aircraft Internal Identifier         | N    | String      | 1/10    |         |
	| CTH | Aircraft Cumulative Total Flight Hours  | Aircraft Cumulative Total Flight Hours        | N    | Decimal     | 9,2     |         |
	| CTY | Aircraft Cumulative Total Cycles        | Aircraft Cumulative Total Cycles              | N    | Integer     | 1/9     |         |
    |----------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Airframe Manufacturer Code.
     *
     * @return string
     */
    public function get_AID_MFR();
    
    /**
     * Get the Aircraft Model.
     *
     * @return string
     */
    public function get_AID_AMC();
    
    /**
     * Get the Airframe Manufacturer Name.
     *
     * @return string
     */
    public function get_AID_MFN();
    
    /**
     * Get the Aircraft Series.
     *
     * @return string
     */
    public function get_AID_ASE();
    
    /**
     * Get the Aircraft Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_AID_AIN();
    
    /**
     * Get the Aircraft Registration Number.
     *
     * @return string
     */
    public function get_AID_REG();
    
    /**
     * Get the Operator Aircraft Internal Identifier.
     *
     * @return string
     */
    public function get_AID_OIN();
    
    /**
     * Get the Aircraft Cumulative Total Flight Hours.
     *
     * @return float
     */
    public function get_AID_CTH();
    
    /**
     * Get the Aircraft Cumulative Total Cycles.
     *
     * @return integer
     */
    public function get_AID_CTY();
}