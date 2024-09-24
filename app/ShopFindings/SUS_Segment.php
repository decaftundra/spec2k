<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\SUS_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SUS_Segment extends ShopFindingsSegment implements SUS_SegmentInterface
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'SUS_Segments';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'SHD'
    ];
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_SUS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'SHD',
        	'MFR',
        	'MPN',
        	'SER',
        	'MFN',
        	'PDT',
        	'PNR',
        	'OPN',
        	'USN',
        	'ASN',
        	'UCN',
        	'SPL',
        	'UST',
        	'PML',
        	'PSC'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('shipped-lru.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Shipped LRU Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('SUS_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.SUS_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->SUS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('SUS_Segment', $this, $shopFindingId);
        
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
        $SUS_Segment = SUS_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $SUS_Segment->SHD = isset($data['SHD']) && $data['SHD'] ? Carbon::createFromFormat('d/m/Y', $data['SHD']) : NULL;
        $SUS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $SUS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $SUS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $SUS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $SUS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $SUS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $SUS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $SUS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $SUS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $SUS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $SUS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $SUS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $SUS_Segment->PSC = isset($data['PSC']) ? $data['PSC'] : NULL;
        $SUS_Segment->PML = isset($data['PML']) ? $data['PML'] : NULL;
        $SUS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $SUS_Segment->save();
    }
    
    /**
     * Get the Shipped Date.
     *
     * @return date
     */
    public function get_SUS_SHD()
    {
        return $this->SHD ? (string) $this->SHD->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Shipped Part Manufacturer Code.
     *
     * @return string
     */
    public function get_SUS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Shipped Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_SUS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Shipped Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_SUS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Shipped Part Manufacturer Name.
     *
     * @return string
     */
    public function get_SUS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Shipped Manufacturer Part Description.
     *
     * @return string
     */
    public function get_SUS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
    
    /**
     * Get the Shipped Manufacturer Part Number.
     *
     * @return string
     */
    public function get_SUS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_SUS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Shipped Universal Serial Number.
     *
     * @return string
     */
    public function get_SUS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Shipped Operator Part Number.
     *
     * @return string
     */
    public function get_SUS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Shipped Operator Serial Number.
     *
     * @return string
     */
    public function get_SUS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_SUS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Shipped Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_SUS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
    
    /**
     * Get the Shipped Part Modification Level.
     *
     * @return string
     */
    public function get_SUS_PML()
    {
        return mb_strlen(trim($this->PML)) ? (string) trim($this->PML) : NULL;
    }
    
    /**
     * Get the Shipped Part Status Code.
     *
     * @return string
     */
    public function get_SUS_PSC()
    {
        return mb_strlen(trim($this->PSC)) ? (string) trim($this->PSC) : NULL;
    }
}
