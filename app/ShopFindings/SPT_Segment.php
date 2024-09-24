<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\SPT_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SPT_Segment extends ShopFindingsSegment implements SPT_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------------------|
    | SPT = Shop Processing Time                                                                                 |
    |------------------------------------------------------------------------------------------------------------|
	| MAH | Shop Total Labor Hours      | Total Labor Hours               | N   | Decimal     | 8,2     | 110.00 |
	| FLW | Shop Flow Time              | Shop Flow Time                  | N   | Integer     | 1/9     |        |
	| MST | Shop Turn Around Time       | Mean Shop Processing Time       | N   | Integer     | 1/4     |        |
	|------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'SPT_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_SPT_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'MAH',
        	'FLW',
        	'MST'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('shop-processing-time.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Shop Processing Time Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('SPT_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.SPT_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->SPT_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('SPT_Segment', $this, $shopFindingId);
        
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
        $SPT_Segment = SPT_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $SPT_Segment->MAH = isset($data['MAH']) ? $data['MAH'] : NULL;
        $SPT_Segment->FLW = isset($data['FLW']) ? $data['FLW'] : NULL;
        $SPT_Segment->MST = isset($data['MST']) ? $data['MST'] : NULL;
        $SPT_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $SPT_Segment->save();
    }
    
    /**
     * Get the Shop Total Labor Hours.
     *
     * @return float
     */
    public function get_SPT_MAH()
    {
        return mb_strlen(trim($this->MAH)) ? (float) $this->MAH : NULL;
    }
    
    /**
     * Get the Shop Flow Time.
     *
     * @return integer
     */
    public function get_SPT_FLW()
    {
        return mb_strlen(trim($this->FLW)) ? (int) $this->FLW : NULL;
    }
    
    /**
     * Get the Shop Turn Around Time.
     *
     * @return integer
     */
    public function get_SPT_MST()
    {
        return mb_strlen(trim($this->MST)) ? (int) $this->MST : NULL;
    }
}
