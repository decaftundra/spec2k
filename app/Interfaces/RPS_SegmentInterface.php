<?php

namespace App\Interfaces;

interface RPS_SegmentInterface
{
    /*
    |---------------------------------------------------------------------------------------------------------------------------------------------|
    | WPS = Worked Piece Part                                                                                                                     |
    |---------------------------------------------------------------------------------------------------------------------------------------------|
	| SFI | Shop Finding Record Identifier                          | Shop Findings Record Identifier         | Y   | String  | 1/50          |   |
	| PPI | Piece Part Record Identifier                            | Piece Part Record Identifier            | Y   | String  | 1/50          |   |
	| PFC | Primary Piece Part Failure Indicator                    | Primary Piece Part Failure Indicator    | Y   | String  | 1/1           | Y |
	| MFR | Failed Piece Part Vendor Code                           | Manufacturer Code                       | N   | String  | 5/5           |   |
	| MFN | Failed Piece Part Vendor Name                           | Manufacturer Name                       | N   | String  | 1/55          |   |
	| MPN | Failed Piece Part Manufacturer Full Length Part Number  | Manufacturer Full Length Part Number    | N   | String  | 1/32          |   |
	| SER | Failed Piece Part Serial Number                         | Part Serial Number                      | N   | String  | 1/15          |   |
	| FDE | Piece Part Failure Description                          | Piece Part Failure Description          | N   | String  | 1/1000        |   |
	| PNR | Vendor Piece Part Number                                | Part Number                             | N   | String  | 1/15          |   |
	| OPN | Overlength Part Number                                  | Overlength Part Number                  | N   | String  | 16/32         |   |
	| USN | Piece Part Universal Serial Number                      | Universal Serial Number                 | N   | String  | 6/20          |   |
	| PDT | Failed Piece Part Description                           | Part Description                        | N   | String  | 1/100         |   |
	| GEL | Piece Part Reference Designator Symbol                  | Geographic and/or Electrical Location   | N   | String  | 1/30          |   |
	| MRD | Received Date                                           | Material Receipt Date                   | N   | Date    | YYYY-MM-DD    |   |
	| ASN | Operator Piece Part Number                              | Airline Stock Number                    | N   | String  | 1/32          |   |
	| UCN | Operator Piece Part Serial Number                       | Unique Component Identification Number  | N   | String  | 1/15          |   |
	| SPL | Supplier Code                                           | Supplier Code                           | N   | String  | 5/5           |   |
	| UST | Piece Part Universal Serial Tracking Number             | Universal Serial Tracking Number        | N   | String  | 6/20          |   |
	|---------------------------------------------------------------------------------------------------------------------------------------------|
	*/
    
    /**
     * Get the Replaced Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RPS_MPN();
    
    /**
     * Get the Replaced Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_RPS_MFR();
    
    /**
     * Get the Replaced Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_RPS_MFN();
    
    /**
     * Get the Replaced Vendor Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_SER();
    
    /**
     * Get the Replaced Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RPS_OPN();
    
    /**
     * Get the Replaced Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_RPS_USN();
    
    /**
     * Get the Replaced Operator Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_ASN();
    
    /**
     * Get the Replaced Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RPS_SPL();
    
    /**
     * Get the Replaced Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RPS_UST();
    
    /**
     * Get the Replaced Vendor Piece Part Description.
     *
     * @return string
     */
    public function get_RPS_PDT();
}