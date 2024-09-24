<?php

namespace App\Interfaces;

interface RLS_SegmentInterface
{
    /*
    |-------------------------------------------------------------------------------------------------------------------------------------------------------|
    | RLS = Removed LRU                                                                                                                                     |
    |-------------------------------------------------------------------------------------------------------------------------------------------------------|
    | MFR | Removed Part Manufacturer Code                  | Manufacturer Code                       | Y | String | 5/5        |                           |
    | MPN | Removed Manufacturer Full Length Part Number    | Manufacturer Full Length Part Number    | Y | String | 1/32       |                           |
    | SER | Removed Manufacturer Serial Number              | Part Serial Number                      | Y | String | 1/15       |                           |
    | RED | Removal Date                                    | Part Removal Date                       | N | Date   | YYYY-MM-DD |                           |
    | TTY | Removal Type Code                               | Removal Type Code                       | N | String | 1/1        | S                         |
    | RET | Removal Type Text                               | Reason for Removal Clarification Text   | N | String | 1/64       |                           |
    | DOI | Install Date of Removed Part                    | Installation Date                       | N | Date   | 2001-06-01 |                           |
    | MFN | Removed Part Manufacturer Name                  | Manufacturer Name                       | N | String | 1/55       | Honeywell                 |
    | PNR | Removed Manufacturer Part Number                | Part Number                             | N | String | 1/15       |                           |
    | OPN | Overlength Part Number                          | Overlength Part Number                  | N | String | 16/32      |                           |
    | USN | Removed Universal Serial Number                 | Universal Serial Number                 | N | String | 6/20       |                           |
    | RMT | Removal Reason Text                             | Removal Reason Text                     | N | String | 1/5000     |                           |
    | APT | Engine/APU Position Identifier                  | Aircraft Engine/APU Position Text       | N | String | 1/100      |                           |
    | CPI | Part Position Code                              | Component Position Code                 | N | String | 1/25       | LB061                     |
    | CPT | Part Position                                   | Component Position Text                 | N | String | 1/100      | Passenger door sect 15    |
    | PDT | Removed Part Description                        | Part Description                        | N | String | 1/100      |                           |
    | PML | Removed Part Modification Level                 | Part Modification Level                 | N | String | 1/100      |                           |
    | ASN | Removed Operator Part Number                    | Airline Stock Number                    | N | String | 1/32       |                           |
    | UCN | Removed Operator Serial Number                  | Unique Component Identification Number  | N | String | 1/15       |                           |
    | SPL | Supplier Code                                   | Supplier Code                           | N | String | 5/5        |                           |
    | UST | Removed Universal Serial Tracking Number        | Universal Serial Tracking Number        | N | String | 6/20       |                           |
    | RFR | Removal Reason Code                             | Reason for Removal Code                 | N | String | 2/2        |                           |
    |-------------------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Removed Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RLS_MFR();
    
    /**
     * Get the Removed Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RLS_MPN();
    
    /**
     * Get the Removed Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RLS_SER();
    
    /**
     * Get the Removal Date.
     *
     * @return date
     */
    public function get_RLS_RED();
    
    /**
     * Get the Removal Type Code.
     *
     * @return string
     */
    public function get_RLS_TTY();
    
    /**
     * Get the Removal Type Text.
     *
     * @return string
     */
    public function get_RLS_RET();
    
    /**
     * Get the Install Date of Removed Part.
     *
     * @return date
     */
    public function get_RLS_DOI();
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RLS_MFN();
    
    /**
     * Get the Removed Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RLS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RLS_OPN();
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RLS_USN();
    
    /**
     * Get the Removal Reason Text.
     *
     * @return string
     */
    public function get_RLS_RMT();
    
    /**
     * Get the Engine/APU Position Identifier.
     *
     * @return string
     */
    public function get_RLS_APT();
    
    /**
     * Get the Part Position Code.
     *
     * @return string
     */
    public function get_RLS_CPI();
    
    /**
     * Get the Part Position.
     *
     * @return string
     */
    public function get_RLS_CPT();
    
    /**
     * Get the Removed Part Description.
     *
     * @return string
     */
    public function get_RLS_PDT();
    
    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RLS_PML();
    
    /**
     * Get the Removed Operator Part Number.
     *
     * @return string
     */
    public function get_RLS_ASN();
    
    /**
     * Get the Removed Operator Serial Number.
     *
     * @return string
     */
    public function get_RLS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RLS_SPL();
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RLS_UST();
    
    /**
     * Get the Removal Reason Code.
     *
     * @return string
     */
    public function get_RLS_RFR();
}