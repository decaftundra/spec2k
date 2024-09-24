<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\AID_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AID_Segment extends ShopFindingsSegment implements AID_SegmentInterface
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
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_AID_';
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
        	'AMC',
        	'MFN',
        	'ASE',
        	'AIN',
        	'REG',
        	'OIN',
        	'CTH',
        	'CTY'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('airframe-information.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Airframe Information Segment';
    }
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'AID_Segments';
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('AID_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.AID_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->AID_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('AID_Segment', $this, $shopFindingId);
        
        $validator = Validator::make($modelArray, $profiler->getValidationRules($shopFindingId), $profiler->getValidationMessages(), $profiler->getFormAttributes());
        
        // Add any conditional validation.
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
        $AID_Segment = AID_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $AID_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $AID_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $AID_Segment->AMC = isset($data['AMC']) ? $data['AMC'] : NULL;
        $AID_Segment->ASE = isset($data['ASE']) ? $data['ASE'] : NULL;
        $AID_Segment->AIN = isset($data['AIN']) ? $data['AIN'] : NULL;
        $AID_Segment->REG = isset($data['REG']) ? $data['REG'] : NULL;
        $AID_Segment->OIN = isset($data['OIN']) ? $data['OIN'] : NULL;
        $AID_Segment->CTH = isset($data['CTH']) ? $data['CTH'] : NULL;
        $AID_Segment->CTY = isset($data['CTY']) ? $data['CTY'] : NULL;
        $AID_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $AID_Segment->save();
    }
    
    /**
     * Get the Airframe Manufacturer Code.
     *
     * @return string
     */
    public function get_AID_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Aircraft Model.
     *
     * @return string
     */
    public function get_AID_AMC()
    {
        return mb_strlen(trim($this->AMC)) ? (string) trim($this->AMC) : NULL;
    }
    
    /**
     * Get the Airframe Manufacturer Name.
     *
     * @return string
     */
    public function get_AID_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Aircraft Series.
     *
     * @return string
     */
    public function get_AID_ASE()
    {
        return mb_strlen(trim($this->ASE)) ? (string) trim($this->ASE) : NULL;
    }
    
    /**
     * Get the Aircraft Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_AID_AIN()
    {
        return mb_strlen(trim($this->AIN)) ? (string) trim($this->AIN) : NULL;
    }
    
    /**
     * Get the Aircraft Registration Number.
     *
     * @return string
     */
    public function get_AID_REG()
    {
        return mb_strlen(trim($this->REG)) ? (string) trim($this->REG) : NULL;
    }
    
    /**
     * Get the Operator Aircraft Internal Identifier.
     *
     * @return string
     */
    public function get_AID_OIN()
    {
        return mb_strlen(trim($this->OIN)) ? (string) trim($this->OIN) : NULL;
    }
    
    /**
     * Get the Aircraft Cumulative Total Flight Hours.
     *
     * @return float
     */
    public function get_AID_CTH()
    {
        return mb_strlen(trim($this->CTH)) ? (float) $this->CTH : NULL;
    }
    
    /**
     * Get the Aircraft Cumulative Total Cycles.
     *
     * @return integer
     */
    public function get_AID_CTY()
    {
        return mb_strlen(trim($this->CTY)) ? (int) $this->CTY : NULL;
    }
}
