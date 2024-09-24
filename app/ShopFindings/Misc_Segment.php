<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\ValidationProfiles\UtasProfile;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class Misc_Segment extends ShopFindingsSegment
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Misc_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_MISC_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'values'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('misc-segment.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View ' . static::getName($this->getShopFindingId()) . ' Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('Misc_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.Misc_Segment')->find($id);
        
        $model = $shopFinding->ShopFindingsDetail->Misc_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = json_decode($this->values, true);
        
        $shopFinding = ShopFinding::with('ShopFindingsDetail.RCS_Segment')->find($shopFindingId);
        $RCS_Segment = $shopFinding->ShopFindingsDetail->RCS_Segment ?? NULL;
        
        $profiler = new ValidationProfiler('Misc_Segment', $this, $shopFindingId);
        
        // Hacky way to get type. Is there another way?
        if ((self::getName($shopFindingId) == UtasProfile::MISC_SEGMENT_NAME) && $RCS_Segment) {
            $modelArray['Type'] = $RCS_Segment->get_RCS_RRC();
        }
        
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
        $Misc_Segment = Misc_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $Misc_Segment->values = is_array($data) ? json_encode($data) : json_encode([]);
        $Misc_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $Misc_Segment->save();
    }
    
    /**
     * Get the name of the miscellaneous segment.
     *
     * @param (string) $notificationId
     * @return string
     */
    public static function getName($notificationId)
    {
        $profiler = new ValidationProfiler('Misc_Segment', (new static), $notificationId);
        
        return $profiler->getMiscSegmentName();
    }
    
    /**
     * Has the notification got a miscellaneous segment.
     *
     * @param (string) $notificationId
     * @return boolean
     */
    public static function hasMiscSegment($notificationId)
    {
        $profiler = new ValidationProfiler('Misc_Segment', (new static), $notificationId);
        
        return $profiler->hasMiscSegment();
    }
    
    // All keys and values are dynamic and stored as jsonb in the DB. So getters must be dynamic.
    
    /**
     * Get value from json string.
     *
     * @param (type) $name
     * @param (array) $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (stristr($name, 'get_MISC_')) {

            $key = str_replace('get_MISC_', '', $name);
            
            if (!$key) throw new \Exception('No Misc_Segment key for: ' . $name);
            
            $decoded = json_decode($this->values);
            
            if (!$decoded) throw new \Exception('Misc_Segment values could not be decoded.');
            
            return $decoded->{$key} ?? NULL;
        }
        
        return parent::__call($name, $arguments);
    }
}
