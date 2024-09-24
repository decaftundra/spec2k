<?php

namespace App\Interfaces;

interface RCS_SegmentInterface {
    
    /*
    |------------------------------------------------------------------------------------------------------------------------------------------------------------|
    | RCS = Received LRU                                                                                                                                         |
    |------------------------------------------------------------------------------------------------------------------------------------------------------------|
	| SFI | Shop Findings Record Identifier                         | Shop Findings Record Identifier                 | Y | String  | 1/50          |            |
	| MRD | Shop Received Date                                      | Material Receipt Date                           | Y | Date    | YYYY-MM-DD    |            |
	| MFR | Received Part Manufacturer Code                         | Manufacturer Code                               | Y | String  | 5/5           |            |
	| MPN | Received Manufacturer Full Length Part Number           | Manufacturer Full Length Part Number            | Y | String  | 1/32          |            |
	| SER | Received Manufacturer Serial Number                     | Part Serial Number                              | Y | String  | 1/15          |            |
	| RRC | Supplier Removal Type Code                              | Supplier Removal Type Code                      | Y | String  | 1/1           | S          |
	| FFC | Failure/ Fault Found                                    | Failure/Fault Found Code                        | Y | String  | 1/2           | FT         |
	| FFI | Failure/ Fault Induced                                  | Failure/Fault Induced Code                      | Y | String  | 1/2           | NI         |
	| FCR | Failure/ Fault Confirms Reason For Removal              | Failure/Fault Confirm Reason Code               | Y | String  | 1/2           | CR         |
	| FAC | Failure/ Fault Confirms Aircraft Message                | Failure/Fault Confirm Aircraft Message Code     | Y | String  | 1/2           | NA         |
	| FBC | Failure/ Fault Confirms Aircraft Part Bite Message      | Failure/Fault Confirm Bite Message Code         | Y | String  | 1/2           | NB         |
	| FHS | Hardware/Software Failure                               | Hardware/Software Failure Code                  | Y | String  | 1/2           | SW         |
	| MFN | Removed Part Manufacturer Name                          | Manufacturer Name                               | N | String  | 1/55          | Honeywell  |
	| PNR | Received Manufacturer Part Number                       | Part Number                                     | N | String  | 1/15          |            |
	| OPN | Overlength Part Number                                  | Overlength Part Number                          | N | String  | 16/32         |            |
	| USN | Removed Universal Serial Number                         | Universal Serial Number                         | N | String  | 6/20          |            |
	| RET | Supplier Removal Type Text                              | Reason for Removal Clarification Text           | N | String  | 1/64          |            |
	| CIC | Customer Code                                           | Customer Identification Code                    | N | String  | 3/5           | UAL        |
	| CPO | Repair Order Identifier                                 | Customer Order Number                           | N | String  | 1/11          | 123UA13    |
	| PSN | Packing Sheet Number                                    | Packing Sheet Number                            | N | String  | 1/15          | 123UA13PS1 |
	| WON | Work Order Number                                       | Work Order Number                               | N | String  | 1/20          | 123UA13WO1 |
	| MRN | Maintenance Release Authorization Number                | Maintenance Release Authorization Number        | N | String  | 1/32          | 123UA13MR1 |
	| CTN | Contract Number                                         | Contract Number                                 | N | String  | 4/15          | 123UA13CT1 |
	| BOX | Master Carton Number                                    | Master Carton Number                            | N | String  | 1/10          | 123UA13BX1 |
	| ASN | Received Operator Part Number                           | Airline Stock Number                            | N | String  | 1/32          |            |
	| UCN | Received Operator Serial Number                         | Unique Component Identification Number          | N | String  | 1/15          |            |
	| SPL | Supplier Code                                           | Supplier Code                                   | N | String  | 5/5           |            |
	| UST | Removed Universal Serial Tracking Number                | Universal Serial Tracking Number                | N | String  | 6/20          |            |
	| PDT | Manufacturer Part Description                           | Part Description                                | N | String  | 1/100         |            |
	| PML | Removed Part Modificiation Level                        | Part Modification Level                         | N | String  | 1/100         |            |
	| SFC | Shop Findings Code                                      | Shop Findings Code                              | N | String  | 1/10          |            |
	| RSI | Related Shop Finding Record Identifier                  | Related Shop Findings Record Identifier         | N | String  | 1/50          |            |
	| RLN | Repair Location Name                                    | Repair Location Name                            | N | String  | 1/25          |            |
	| INT | Incoming Inspection Text                                | Incoming Inspection/Shop Action Text            | N | String  | 1/5000        |            |
	| REM | Comment Text                                            | Remarks Text                                    | N | String  | 1/1000        |            |
	|------------------------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Shop Findings Record Identifier.
     *
     * @return string
     */
    public function get_RCS_SFI();
    
    /**
     * Get the Shop Received Date .
     *
     * @return date
     */
    public function get_RCS_MRD();
    
    /**
     * Get the Received Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RCS_MFR();
    
    /**
     * Get the Received Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RCS_MPN();
    
    /**
     * Get the Received Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RCS_SER();
    
    /**
     * Get the Supplier Removal Type Code.
     *
     * @return string
     */
    public function get_RCS_RRC();
    
    /**
     * Get the Failure/Fault Found.
     *
     * @return string
     */
    public function get_RCS_FFC();
    
    /**
     * Get the Failure/Fault Induced.
     *
     * @return string
     */
    public function get_RCS_FFI();
    
    /**
     * Get the Failure/Fault Confirms Reason For Removal.
     *
     * @return string
     */
    public function get_RCS_FCR();
    
    /**
     * Get the Failure/Fault Confirms Aircraft Message.
     *
     * @return string
     */
    public function get_RCS_FAC();
    
    /**
     * Get the Failure/Fault Confirms Aircraft Part Bite Message.
     *
     * @return string
     */
    public function get_RCS_FBC();
    
    /**
     * Get the Hardware/Software Failure.
     *
     * @return string
     */
    public function get_RCS_FHS();
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RCS_MFN();
    
    /**
     * Get the Received Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RCS_PNR();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RCS_OPN();
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RCS_USN();
    
    /**
     * Get the Supplier Removal Type Text.
     *
     * @return string
     */
    public function get_RCS_RET();
    
    /**
     * Get the Customer Code.
     *
     * @return string
     */
    public function get_RCS_CIC();
    
    /**
     * Get the Repair Order Identifier.
     *
     * @return string
     */
    public function get_RCS_CPO();
    
    /**
     * Get the Packing Sheet Number.
     *
     * @return string
     */
    public function get_RCS_PSN();
    
    /**
     * Get the Work Order Number.
     *
     * @return string
     */
    public function get_RCS_WON();
    
    /**
     * Get the Maintenance Release Authorization Number.
     *
     * @return string
     */
    public function get_RCS_MRN();
    
    /**
     * Get the Contract Number.
     *
     * @return string
     */
    public function get_RCS_CTN();
    
    /**
     * Get the Master Carton Number.
     *
     * @return string
     */
    public function get_RCS_BOX();
    
    /**
     * Get the Received Operator Part Number.
     *
     * @return string
     */
    public function get_RCS_ASN();
    
    /**
     * Get the Received Operator Serial Number.
     *
     * @return string
     */
    public function get_RCS_UCN();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RCS_SPL();
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RCS_UST();
    
    /**
     * Get the Manufacturer Part Description.
     *
     * @return string
     */
    public function get_RCS_PDT();
    
    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RCS_PML();
    
    /**
     * Get the Shop Findings Code.
     *
     * @return string
     */
    public function get_RCS_SFC();
    
    /**
     * Get the Related Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_RCS_RSI();
    
    /**
     * Get the Repair Location Name.
     *
     * @return string
     */
    public function get_RCS_RLN();
    
    /**
     * Get the Incoming Inspection Text.
     *
     * @return string
     */
    public function get_RCS_INT();
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_RCS_REM();
}