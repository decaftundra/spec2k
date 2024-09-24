<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class RCS_Segment extends ShopFindingsSegment implements RCS_SegmentInterface
{
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RCS_Segments';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'MRD'
    ];
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_RCS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'SFI',
        	'MRD',
        	'MFR',
        	'MPN',
        	'SER',
        	'RRC',
        	'FFC',
        	'FFI',
        	'FCR',
        	'FAC',
        	'FBC',
        	'FHS',
        	'MFN',
        	'PNR',
        	'OPN',
        	'USN',
        	'RET',
        	'CIC',
        	'CPO',
        	'PSN',
        	'WON',
        	'MRN',
        	'CTN',
        	'BOX',
        	'ASN',
        	'UCN',
        	'SPL',
        	'UST',
        	'PDT',
        	'PML',
        	'SFC',
        	'RSI',
        	'RLN',
        	'INT',
        	'REM'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('received-lru.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Received LRU Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('RCS_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.RCS_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->RCS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('RCS_Segment', $this, $shopFindingId);
        
        $validator = Validator::make($modelArray, $profiler->getValidationRules($shopFindingId), $profiler->getValidationMessages(), $profiler->getFormAttributes());
        
        // Add conditional validation.
        $validatedConditionally = $profiler->conditionalValidation($validator);
        
        if ($validatedConditionally->fails()) {
            $this->validationErrors = $validatedConditionally->errors()->all();
            return false;
        }
        
        return true;
    }
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $shopFindingsDetailId
     * @return void
     */
    public static function createOrUpdateSegment(array $data, string $shopFindingsDetailId, $autosave = null)
    {
        $RCS_Segment = RCS_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        
        $RCS_Segment->SFI = isset($data['SFI']) ? $data['SFI'] : NULL;
        $RCS_Segment->MRD = isset($data['MRD']) && $data['MRD'] ? Carbon::createFromFormat('d/m/Y', $data['MRD']) : NULL;
        $RCS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $RCS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $RCS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $RCS_Segment->RRC = isset($data['RRC']) ? $data['RRC'] : NULL;
        $RCS_Segment->FFC = isset($data['FFC']) ? $data['FFC'] : NULL;
        $RCS_Segment->FFI = isset($data['FFI']) ? $data['FFI'] : NULL;
        $RCS_Segment->FHS = isset($data['FHS']) ? $data['FHS'] : NULL;
        $RCS_Segment->FCR = isset($data['FCR']) ? $data['FCR'] : NULL;
        $RCS_Segment->FAC = isset($data['FAC']) ? $data['FAC'] : NULL;
        $RCS_Segment->FBC = isset($data['FBC']) ? $data['FBC'] : NULL;
        $RCS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $RCS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $RCS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $RCS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $RCS_Segment->RET = isset($data['RET']) ? $data['RET'] : NULL;
        $RCS_Segment->CIC = isset($data['CIC']) ? $data['CIC'] : NULL;
        $RCS_Segment->CPO = isset($data['CPO']) ? $data['CPO'] : NULL;
        $RCS_Segment->PSN = isset($data['PSN']) ? $data['PSN'] : NULL;
        $RCS_Segment->WON = isset($data['WON']) ? $data['WON'] : NULL;
        $RCS_Segment->MRN = isset($data['MRN']) ? $data['MRN'] : NULL;
        $RCS_Segment->CTN = isset($data['CTN']) ? $data['CTN'] : NULL;
        $RCS_Segment->BOX = isset($data['BOX']) ? $data['BOX'] : NULL;
        $RCS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $RCS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $RCS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $RCS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $RCS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $RCS_Segment->PML = isset($data['PML']) ? $data['PML'] : NULL;
        $RCS_Segment->RSI = isset($data['RSI']) ? $data['RSI'] : NULL;
        $RCS_Segment->SFC = isset($data['SFC']) ? $data['SFC'] : NULL;
        $RCS_Segment->RLN = isset($data['RLN']) ? $data['RLN'] : NULL;
        $RCS_Segment->INT = isset($data['INT']) ? $data['INT'] : NULL;
        $RCS_Segment->REM = isset($data['REM']) ? $data['REM'] : NULL;
        $RCS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $RCS_Segment->save();
    }
    
    /**
     * Extra validation to make sure at least one Piece Part has a fail ID of 'Y' in certain conditions.
     *
     * @return boolean
     */
    public function piecePartHasFailed()
    {
        if (
            ($this->get_RCS_RRC() == 'U') && 
            ($this->get_RCS_FHS() == 'HW') && 
            ($this->get_RCS_FFC() == 'FT') && 
            ($this->get_RCS_FCR() == 'CR')
        ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Extra validation to make sure no piece parts have a fail ID of 'Y' when a piece part was Scheduled, Modified or Other.
     *
     * @return boolean
     */
    public function piecePartHasNotFailed()
    {
        return in_array($this->get_RCS_RRC(), ['O', 'M', 'S']);
    }
    
    /**
     * Get the Shop Findings Record Identifier.
     *
     * @return string
     */
    public function get_RCS_SFI()
    {
        return $this->SFI;
    }
    
    /**
     * Get the Shop Received Date .
     *
     * @return date
     */
    public function get_RCS_MRD()
    {
        return $this->MRD ? (string) $this->MRD->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Received Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RCS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Received Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RCS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Received Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RCS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Supplier Removal Type Code.
     *
     * @return string
     */
    public function get_RCS_RRC()
    {
        return mb_strlen(trim($this->RRC)) ? (string) trim($this->RRC) : NULL;
    }
    
    /**
     * Get the Failure/Fault Found.
     *
     * @return string
     */
    public function get_RCS_FFC()
    {
        return mb_strlen(trim($this->FFC)) ? (string) trim($this->FFC) : NULL;
    }
    
    /**
     * Get the Failure/Fault Induced.
     *
     * @return string
     */
    public function get_RCS_FFI()
    {
        return mb_strlen(trim($this->FFI)) ? (string) trim($this->FFI) : NULL;
    }
    
    /**
     * Get the Failure/Fault Confirms Reason For Removal.
     *
     * @return string
     */
    public function get_RCS_FCR()
    {
        return mb_strlen(trim($this->FCR)) ? (string) trim($this->FCR) : NULL;
    }
    
    /**
     * Get the Failure/Fault Confirms Aircraft Message.
     *
     * @return string
     */
    public function get_RCS_FAC()
    {
        return mb_strlen(trim($this->FAC)) ? (string) trim($this->FAC) : NULL;
    }
    
    /**
     * Get the Failure/Fault Confirms Aircraft Part Bite Message.
     *
     * @return string
     */
    public function get_RCS_FBC()
    {
        return mb_strlen(trim($this->FBC)) ? (string) trim($this->FBC) : NULL;
    }
    
    /**
     * Get the Hardware/Software Failure.
     *
     * @return string
     */
    public function get_RCS_FHS()
    {
        return mb_strlen(trim($this->FHS)) ? (string) trim($this->FHS) : NULL;
    }
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RCS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Received Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RCS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RCS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RCS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Supplier Removal Type Text.
     *
     * @return string
     */
    public function get_RCS_RET()
    {
        return mb_strlen(trim($this->RET)) ? (string) trim($this->RET) : NULL;
    }
    
    /**
     * Get the Customer Code.
     *
     * @return string
     */
    public function get_RCS_CIC()
    {
        return mb_strlen(trim($this->CIC)) ? (string) trim($this->CIC) : NULL;
    }
    
    /**
     * Get the Repair Order Identifier.
     *
     * @return string
     */
    public function get_RCS_CPO()
    {
        return mb_strlen(trim($this->CPO)) ? (string) trim($this->CPO) : NULL;
    }
    
    /**
     * Get the Packing Sheet Number.
     *
     * @return string
     */
    public function get_RCS_PSN()
    {
        return mb_strlen(trim($this->PSN)) ? (string) trim($this->PSN) : NULL;
    }
    
    /**
     * Get the Work Order Number.
     *
     * @return string
     */
    public function get_RCS_WON()
    {
        return mb_strlen(trim($this->WON)) ? (string) trim($this->WON) : NULL;
    }
    
    /**
     * Get the Maintenance Release Authorization Number.
     *
     * @return string
     */
    public function get_RCS_MRN()
    {
        return mb_strlen(trim($this->MRN)) ? (string) trim($this->MRN) : NULL;
    }
    
    /**
     * Get the Contract Number.
     *
     * @return string
     */
    public function get_RCS_CTN()
    {
        return mb_strlen(trim($this->CTN)) ? (string) trim($this->CTN) : NULL;
    }
    
    /**
     * Get the Master Carton Number.
     *
     * @return string
     */
    public function get_RCS_BOX()
    {
        return mb_strlen(trim($this->BOX)) ? (string) trim($this->BOX) : NULL;
    }
    
    /**
     * Get the Received Operator Part Number.
     *
     * @return string
     */
    public function get_RCS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Received Operator Serial Number.
     *
     * @return string
     */
    public function get_RCS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RCS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RCS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
    
    /**
     * Get the Manufacturer Part Description.
     *
     * @return string
     */
    public function get_RCS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
    
    /**
     * Get the Removed Part Modificiation Level.
     *
     * @return string
     */
    public function get_RCS_PML()
    {
        return mb_strlen(trim($this->PML)) ? (string) trim($this->PML) : NULL;
    }
    
    /**
     * Get the Shop Findings Code.
     *
     * @return string
     */
    public function get_RCS_SFC()
    {
        return mb_strlen(trim($this->SFC)) ? (string) trim($this->SFC) : NULL;
    }
    
    /**
     * Get the Related Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_RCS_RSI()
    {
        return mb_strlen(trim($this->RSI)) ? (string) trim($this->RSI) : NULL;
    }
    
    /**
     * Get the Repair Location Name.
     *
     * @return string
     */
    public function get_RCS_RLN()
    {
        return mb_strlen(trim($this->RLN)) ? (string) trim($this->RLN) : NULL;
    }
    
    /**
     * Get the Incoming Inspection Text.
     *
     * @return string
     */
    public function get_RCS_INT()
    {
        return mb_strlen(trim($this->INT)) ? (string) trim($this->INT) : NULL;
    }
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_RCS_REM()
    {
        return mb_strlen(trim($this->REM)) ? (string) trim($this->REM) : NULL;
    }
}
