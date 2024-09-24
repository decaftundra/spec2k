<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\API_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class API_Segment extends ShopFindingsSegment implements API_SegmentInterface
{
    /*
    |--------------------------------------------------------------------------------------------------------------------------------|
    | API = APU Information                                                                                                          |
    |--------------------------------------------------------------------------------------------------------------------------------|
    | AET | Aircraft APU Type                       | Aircraft Engine/APU Type            | Y   | String      | 1/20    | 331-400B   |
	| EMS | APU Serial Number                       | Engine/APU Module Serial Number     | Y   | String      | 1/20    | SP-E994180 |
	| AEM | Aircraft APU Model                      | Aircraft Engine/APU Model           | N   | String      | 1/32    | 3800608-2  |
	| MFR | Aircraft Engine Manufacturer Code       | Manufacturer Code                   | N   | String      | 5/5     | 99193      |
	| ATH | APU Cumulative Hours                    | APU Cumulative Total Hours          | N   | Decimal     | 9,2     |            |
	| ATC | APU Cumulative Cycles                   | APU Cumulative Total Cycles         | N   | Integer     | 1/9     |            |
	|--------------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'API_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_API_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'AET',
        	'EMS',
        	'AEM',
        	'MFR',
        	'ATH',
        	'ATC'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('apu-information.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View APU Information Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('AID_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.API_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->API_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('API_Segment', $this, $shopFindingId);
        
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
        $API_Segment = API_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $API_Segment->AET = isset($data['AET']) ? $data['AET'] : NULL;
        $API_Segment->EMS = isset($data['EMS']) ? $data['EMS'] : NULL;
        $API_Segment->AEM = isset($data['AEM']) ? $data['AEM'] : NULL;
        $API_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $API_Segment->ATH = isset($data['ATH']) ? $data['ATH'] : NULL;
        $API_Segment->ATC = isset($data['ATC']) ? $data['ATC'] : NULL;
        $API_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $API_Segment->save();
    }
    
    /**
     * Get the Aircraft APU Type.
     *
     * @return string
     */
    public function get_API_AET()
    {
        return mb_strlen(trim($this->AET)) ? (string) trim($this->AET) : NULL;
    }
    
    /**
     * Get the APU Serial Number.
     *
     * @return string
     */
    public function get_API_EMS()
    {
        return mb_strlen(trim($this->EMS)) ? (string) trim($this->EMS) : NULL;
    }
    
    /**
     * Get the Aircraft APU Model.
     *
     * @return string
     */
    public function get_API_AEM()
    {
        return mb_strlen(trim($this->AEM)) ? (string) trim($this->AEM) : NULL;
    }
    
    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_API_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the APU Cumulative Hours.
     *
     * @return float
     */
    public function get_API_ATH()
    {
        return mb_strlen(trim($this->ATH)) ? (float) $this->ATH : NULL;
    }
    
    /**
     * Get the APU Cumulative Cycles.
     *
     * @return integer
     */
    public function get_API_ATC()
    {
        return mb_strlen(trim($this->ATC)) ? (int) $this->ATC : NULL;
    }
}
