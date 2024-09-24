<?php

namespace App\Interfaces;

interface NHS_SegmentInterface {
    
    /*
    |---------------------------------------------------------------------------------------------------------------------------------------------------------|
    | NHS = Next Higher Assembly                                                                                                                              |
    |---------------------------------------------------------------------------------------------------------------------------------------------------------|
	| MFR | Failed Piece Part Next Higher Assembly Part Manufacturer Code   | Manufacturer Code                                       | Y   | String  | 5/5   |
	| MPN | Next Higher Assembly Manufacturer Full Length Part Number       | Manufacturer Full Length Part Number                    | Y   | String  | 1/32  |
	| SER | Failed Piece Part Next Higher Assembly Serial Number            | Part Serial Number                                      | Y   | String  | 1/15  |
	| MFN | Failed Piece Part Next Higher Assembly Part Manufacturer Name   | Manufacturer Name                                       | N   | String  | 1/55  |
	| PNR | Failed Piece Part Next Higher Assembly Part Number              | Part Number                                             | N   | String  | 1/15  |
	| OPN | Overlength Part Number                                          | Overlength Part Number                                  | N   | String  | 16/32 |
	| USN | Failed Piece Part Universal Serial Number                       | Universal Serial Number                                 | N   | String  | 6/20  |
	| PDT | Failed Piece Part Next Higher Assembly Part Name                | Part Description                                        | N   | String  | 1/100 |
	| ASN | Failed Piece Part Next Higher Assembly Operator Part Number     | Airline Stock Number                                    | N   | String  | 1/32  |
	| UCN | Failed Piece Part Next Higher Assembly Operator Serial Number   | Unique Component Identification Number                  | N   | String  | 1/15  |
	| SPL | Supplier Code                                                   | Supplier Code                                           | N   | String  | 5/5   |
	| UST | Failed Piece Part NHA Universal Serial Tracking Number          | Universal Serial Tracking Number                        | N   | String  | 6/20  |
	| NPN | Failed Piece Part Next Higher Assembly NHA Part Number          | Failed Piece Part Next Higher Assembly NHA Part Number  | N   | String  | 1/32  |
	|---------------------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Code.
     *
     * @return string
     */
    public function get_NHS_MFR();
    
    /**
     * Get the Next Higher Assembly Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_NHS_MPN();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Serial Number.
     *
     * @return string
     */
    public function get_NHS_SER();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Name.
     *
     * @return string
     */
    public function get_NHS_MFN();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Number.
     *
     * @return string
     */
    public function get_NHS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_NHS_OPN();
    
    /**
     * Get the Failed Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_NHS_USN();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Name.
     *
     * @return string
     */
    public function get_NHS_PDT();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Part Number.
     *
     * @return string
     */
    public function get_NHS_ASN();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Serial Number.
     *
     * @return string
     */
    public function get_NHS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_NHS_SPL();
    
    /**
     * Get the Failed Piece Part NHA Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_NHS_UST();
    
    /**
     * Get the Failed Piece Part Next Higher Assembly NHA Part Number.
     *
     * @return string
     */
    public function get_NHS_NPN();
}