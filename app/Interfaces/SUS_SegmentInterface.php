<?php

namespace App\Interfaces;

interface SUS_SegmentInterface
{
    /*
    |-----------------------------------------------------------------------------------------------------------------------------------------------------|
    | SUS = Shipped LRU                                                                                                                                   |
    |-----------------------------------------------------------------------------------------------------------------------------------------------------|
    | SHD | Shipped Date                                    | Shipped Date                                | Y   | Date        | YYYY-MM-DD    |           |
	| MFR | Shipped Part Manufacturer Code                  | Manufacturer Code                           | Y   | String      | 5/5           |           |
	| MPN | Shipped Manufacturer Full Length Part Number    | Manufacturer Full Length Part Number        | Y   | String      | 1/32          |           |
	| SER | Shipped Manufacturer Serial Number              | Part Serial Number                          | Y   | String      | 1/15          |           |
	| MFN | Shipped Part Manufacturer Name                  | Manufacturer Name                           | N   | String      | 1/55          | Honeywell |
	| PDT | Shipped Manufacturer Part Description           | Part Description                            | N   | String      | 1/100         |           |
	| PNR | Shipped Manufacturer Part Number                | Part Number                                 | N   | String      | 1/15          |           |
	| OPN | Overlength Part Number                          | Overlength Part Number                      | N   | String      | 16/32         |           |
	| USN | Shipped Universal Serial Number                 | Universal Serial Number                     | N   | String      | 6/20          |           |
	| ASN | Shipped Operator Part Number                    | Airline Stock Number                        | N   | String      | 1/32          |           |
	| UCN | Shipped Operator Serial Number                  | Unique Component Identification Number      | N   | String      | 1/15          |           |
	| SPL | Supplier Code                                   | Supplier Code                               | N   | String      | 5/5           |           |
	| UST | Shipped Universal Serial Tracking Number        | Universal Serial Tracking Number            | N   | String      | 6/20          |           |
	| PML | Shipped Part Modification Level                 | Part Modification Level                     | N   | String      | 1/100         |           |
	| PSC | Shipped Part Status Code                        | Part Status Code                            | N   | String      | 1/16          |           |
	|-----------------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Shipped Date.
     *
     * @return date
     */
    public function get_SUS_SHD();
    
    /**
     * Get the Shipped Part Manufacturer Code.
     *
     * @return string
     */
    public function get_SUS_MFR();
    
    /**
     * Get the Shipped Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_SUS_MPN();
    
    /**
     * Get the Shipped Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_SUS_SER();
    
    /**
     * Get the Shipped Part Manufacturer Name.
     *
     * @return string
     */
    public function get_SUS_MFN();
    
    /**
     * Get the Shipped Manufacturer Part Description.
     *
     * @return string
     */
    public function get_SUS_PDT();
    
    /**
     * Get the Shipped Manufacturer Part Number.
     *
     * @return string
     */
    public function get_SUS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_SUS_OPN();
    
    /**
     * Get the Shipped Universal Serial Number.
     *
     * @return string
     */
    public function get_SUS_USN();
    
    /**
     * Get the Shipped Operator Part Number.
     *
     * @return string
     */
    public function get_SUS_ASN();
    
    /**
     * Get the Shipped Operator Serial Number.
     *
     * @return string
     */
    public function get_SUS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_SUS_SPL();
    
    /**
     * Get the Shipped Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_SUS_UST();
    
    /**
     * Get the Shipped Part Modification Level.
     *
     * @return string
     */
    public function get_SUS_PML();
    
    /**
     * Get the Shipped Part Status Code.
     *
     * @return string
     */
    public function get_SUS_PSC();
}