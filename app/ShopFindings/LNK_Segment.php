<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\LNK_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LNK_Segment extends ShopFindingsSegment implements LNK_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------|
    | LNK = Linking Fields                                                                           |
    |------------------------------------------------------------------------------------------------|
	| RTI | Removal Tracking Identifier     | Removal Tracking Identifier     | Y   | String  | 1/50 |
	|------------------------------------------------------------------------------------------------|
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'LNK_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_LNK_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'RTI'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('linking-field.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Linking Field Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('LNK_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.LNK_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->LNK_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('LNK_Segment', $this, $shopFindingId);
        
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
        $LNK_Segment = LNK_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $LNK_Segment->RTI = isset($data['RTI']) ? $data['RTI'] : NULL;
        $LNK_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $LNK_Segment->save();
    }
    
    /**
     * Get the Removal Tracking Identifier.
     *
     * @return string
     */
    public function get_LNK_RTI()
    {
        return mb_strlen(trim($this->RTI)) ? (string) trim($this->RTI) : NULL;
    }
}
