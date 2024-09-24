<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\ATT_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ATT_Segment extends ShopFindingsSegment implements ATT_SegmentInterface
{
    /*
    |---------------------------------------------------------------------------------------------------|
    | ATT = Accumulated Time Text (Removed LRU)                                                         |
    |---------------------------------------------------------------------------------------------------|
	| TRF | Time/Cycle Reference Code       | Time/Cycle Reference Code       | Y   | String      | 1/1 |
	| OTT | Operating Time                  | Operating Time                  | N   | Integer     | 1/6 |
	| OPC | Operating Cycle Count           | Operating Cycle Count           | N   | Integer     | 1/6 |
	| ODT | Operating Day Count             | Operating Days                  | N   | Integer     | 1/6 |
	|---------------------------------------------------------------------------------------------------|
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ATT_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_ATT_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'TRF',
        	'OTT',
        	'OPC',
        	'ODT'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('accumulated-time-text.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Accumulated Time Text Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('ATT_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.ATT_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->ATT_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('ATT_Segment', $this, $shopFindingId);
        
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
        $ATT_Segment = ATT_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $ATT_Segment->TRF = isset($data['TRF']) ? $data['TRF'] : NULL;
        $ATT_Segment->OTT = isset($data['OTT']) ? $data['OTT'] : NULL;
        $ATT_Segment->OPC = isset($data['OPC']) ? $data['OPC'] : NULL;
        $ATT_Segment->ODT = isset($data['ODT']) ? $data['ODT'] : NULL;
        $ATT_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $ATT_Segment->save();
    }
    
    /**
     * Get the Time/Cycle Reference Code.
     *
     * @return string
     */
    public function get_ATT_TRF()
    {
        return mb_strlen(trim($this->TRF)) ? (string) trim($this->TRF) : NULL;
    }
    
    /**
     * Get the Operating Time.
     *
     * @return integer
     */
    public function get_ATT_OTT()
    {
        return mb_strlen(trim($this->OTT)) ? (int) $this->OTT : NULL;
    }
    
    /**
     * Get the Operating Cycle Count.
     *
     * @return integer
     */
    public function get_ATT_OPC()
    {
        return mb_strlen(trim($this->OPC)) ? (int) $this->OPC : NULL;
    }
    
    /**
     * Get the Operating Day Count.
     *
     * @return integer
     */
    public function get_ATT_ODT()
    {
        return mb_strlen(trim($this->ODT)) ? (int) $this->ODT : NULL;
    }
}
