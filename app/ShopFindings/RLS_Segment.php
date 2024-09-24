<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\RLS_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class RLS_Segment extends ShopFindingsSegment implements RLS_SegmentInterface
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RLS_Segments';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'RED',
        'DOI'
    ];
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_RLS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'MFR',
            'MPN',
            'SER',
            'RED',
            'TTY',
            'RET',
            'DOI',
            'MFN',
            'PNR',
            'OPN',
            'USN',
            'RMT',
            'APT',
            'CPI',
            'CPT',
            'PDT',
            'PML',
            'ASN',
            'UCN',
            'SPL',
            'UST',
            'RFR'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('removed-lru.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Removed LRU Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('RLS_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.RLS_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->RLS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('RLS_Segment', $this, $shopFindingId);
        
        $validator = Validator::make($modelArray, $profiler->getValidationRules($shopFindingId), $profiler->getValidationMessages(), $profiler->getFormAttributes());
        
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
        $RLS_Segment = RLS_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);

        $RLS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $RLS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $RLS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $RLS_Segment->RED = isset($data['RED']) && $data['RED'] ? Carbon::createFromFormat('d/m/Y', $data['RED']) : NULL;
        $RLS_Segment->TTY = isset($data['TTY']) ? $data['TTY'] : NULL;
        $RLS_Segment->RFR = isset($data['RFR']) ? $data['RFR'] : NULL;
        $RLS_Segment->RET = isset($data['RET']) ? $data['RET'] : NULL;
        $RLS_Segment->DOI = isset($data['DOI']) && $data['DOI'] ? Carbon::createFromFormat('d/m/Y', $data['DOI']) : NULL;
        $RLS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $RLS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $RLS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $RLS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $RLS_Segment->RMT = isset($data['RMT']) ? $data['RMT'] : NULL;
        $RLS_Segment->APT = isset($data['APT']) ? $data['APT'] : NULL;
        $RLS_Segment->CPI = isset($data['CPI']) ? $data['CPI'] : NULL;
        $RLS_Segment->CPT = isset($data['CPT']) ? $data['CPT'] : NULL;
        $RLS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $RLS_Segment->PML = isset($data['PML']) ? $data['PML'] : NULL;
        $RLS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $RLS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $RLS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $RLS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $RLS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $RLS_Segment->save();
    }
    
    /**
     * Get the Removed Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RLS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Removed Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RLS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Removed Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RLS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Removal Date.
     *
     * @return date
     */
    public function get_RLS_RED()
    {
        return $this->RED ? (string) $this->RED->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Removal Type Code.
     *
     * @return string
     */
    public function get_RLS_TTY()
    {
        return mb_strlen(trim($this->TTY)) ? (string) trim($this->TTY) : NULL;
    }
    
    /**
     * Get the Removal Type Text.
     *
     * @return string
     */
    public function get_RLS_RET()
    {
        return mb_strlen(trim($this->RET)) ? (string) trim($this->RET) : NULL;
    }
    
    /**
     * Get the Install Date of Removed Part.
     *
     * @return date
     */
    public function get_RLS_DOI()
    {
        return $this->DOI ? (string) $this->DOI->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RLS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Removed Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RLS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RLS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RLS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Removal Reason Text.
     *
     * @return string
     */
    public function get_RLS_RMT()
    {
        return mb_strlen(trim($this->RMT)) ? (string) trim($this->RMT) : NULL;
    }
    
    /**
     * Get the Engine/APU Position Identifier.
     *
     * @return string
     */
    public function get_RLS_APT()
    {
        return mb_strlen(trim($this->APT)) ? (string) trim($this->APT) : NULL;
    }
    
    /**
     * Get the Part Position Code.
     *
     * @return string
     */
    public function get_RLS_CPI()
    {
        return mb_strlen(trim($this->CPI)) ? (string) trim($this->CPI) : NULL;
    }
    
    /**
     * Get the Part Position.
     *
     * @return string
     */
    public function get_RLS_CPT()
    {
        return mb_strlen(trim($this->CPT)) ? (string) trim($this->CPT) : NULL;
    }
    
    /**
     * Get the Removed Part Description.
     *
     * @return string
     */
    public function get_RLS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
    
    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RLS_PML()
    {
        return mb_strlen(trim($this->PML)) ? (string) trim($this->PML) : NULL;
    }
    
    /**
     * Get the Removed Operator Part Number.
     *
     * @return string
     */
    public function get_RLS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Removed Operator Serial Number.
     *
     * @return string
     */
    public function get_RLS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RLS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RLS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
    
    /**
     * Get the Removal Reason Code.
     *
     * @return string
     */
    public function get_RLS_RFR()
    {
        return mb_strlen(trim($this->RFR)) ? (string) trim($this->RFR) : NULL;
    }
}
