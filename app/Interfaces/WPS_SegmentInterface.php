<?php

namespace App\Interfaces;

interface WPS_SegmentInterface
{
    /*
    WPS = Worked Piece Part
    
	wpsSFI  Shop Finding Record Identifier                              Shop Findings Record Identifier         SFI     Y   String  1/50
	wpsPPI  Piece Part Record Identifier                                Piece Part Record Identifier            PPI     Y   String  1/50
	wpsPFC  Primary Piece Part Failure Indicator                        Primary Piece Part Failure Indicator	PFC     Y   String  1/1	Y
	wpsMFR  Failed Piece Part Vendor Code                               Manufacturer Code                       MFR     N   String  5/5
	wpsMFN  Failed Piece Part Vendor Name                               Manufacturer Name                       MFN     N   String  1/55
	wpsMPN  Failed Piece Part Manufacturer Full Length Part Number      Manufacturer Full Length Part Number    MPN     N   String  1/32
	wpsSER  Failed Piece Part Serial Number                             Part Serial Number                      SER     N   String  1/15
	wpsFDE  Piece Part Failure Description                              Piece Part Failure Description          FDE     N   String  1/1000
	wpsPNR  Vendor Piece Part Number                                    Part Number                             PNR     N   String  1/15
	wpsOPN  Overlength Part Number                                      Overlength Part Number                  OPN     N   String  16/32
	wpsUSN  Piece Part Universal Serial Number                          Universal Serial Number                 USN     N   String  6/20
	wpsPDT  Failed Piece Part Description                               Part Description                        PDT     N   String  1/100
	wpsGEL  Piece Part Reference Designator Symbol                      Geographic and/or Electrical Location   GEL     N   String  1/30
	wpsMRD  Received Date                                               Material Receipt Date                   MRD     N   Date    YYYY-MM-DD
	wpsASN  Operator Piece Part Number                                  Airline Stock Number                    ASN     N   String  1/32
	wpsUCN  Operator Piece Part Serial Number                           Unique Component Identification Number  UCN     N   String  1/15
	wpsSPL  Supplier Code                                               Supplier Code                           SPL     N   String  5/5
	wpsUST  Piece Part Universal Serial Tracking Number                 Universal Serial Tracking Number        UST     N   String  6/20
	*/
	
	/**
     * Get the Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_WPS_SFI();
    
    /**
     * Get the Piece Part Record Identifier.
     *
     * @return string
     */
    public function get_WPS_PPI();
    
    /**
     * Get the Primary Piece Part Failure Indicator.
     *
     * @return string
     */
    public function get_WPS_PFC();
    
    /**
     * Get the Failed Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_WPS_MFR();
    
    /**
     * Get the Failed Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_WPS_MFN();
    
    /**
     * Get the Failed Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_WPS_MPN();
    
    /**
     * Get the Failed Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_SER();
    
    /**
     * Get the Piece Part Failure Description.
     *
     * @return string
     */
    public function get_WPS_FDE();
    
    /**
     * Get the Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_WPS_OPN();
    
    /**
     * Get the Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_WPS_USN();
    
    /**
     * Get the Failed Piece Part Description.
     *
     * @return string
     */
    public function get_WPS_PDT();
    
    /**
     * Get the Piece Part Reference Designator Symbol.
     *
     * @return string
     */
    public function get_WPS_GEL();
    
    /**
     * Get the Received Date.
     *
     * @return date
     */
    public function get_WPS_MRD();
    
    /**
     * Get the Operator Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_ASN();
    
    /**
     * Get the Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_WPS_SPL();
    
    /**
     * Get the Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_WPS_UST();
}