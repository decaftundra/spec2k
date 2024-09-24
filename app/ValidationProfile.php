<?php

namespace App;

use App\Traits\SegmentTraits;
use App\Interfaces\SegmentInterface;
use Illuminate\Validation\Validator;

abstract class ValidationProfile
{
    use SegmentTraits;
    
    protected static $segmentNames = [
        'HDR_Segment',
        'AID_Segment',
        'EID_Segment',
        'API_Segment',
        'RCS_Segment',
        'SAS_Segment',
        'SUS_Segment',
        'RLS_Segment',
        'LNK_Segment',
        'ATT_Segment',
        'SPT_Segment',
        'WPS_Segment',
        'NHS_Segment',
        'RPS_Segment',
        'Misc_Segment'
    ];
    
    protected $segment;
    protected $segmentName;
    protected $notificationId;
    protected $HDR_Segment;
    protected $AID_Segment;
    protected $EID_Segment;
    protected $API_Segment;
    protected $RCS_Segment;
    protected $SAS_Segment;
    protected $SUS_Segment;
    protected $RLS_Segment;
    protected $LNK_Segment;
    protected $ATT_Segment;
    protected $SPT_Segment;
    protected $Misc_Segment;
    protected $WPS_Segment;
    protected $NHS_Segment;
    protected $RPS_Segment;
    
    /**
     * Set the segment name, segment object and notification ID.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (string) $notificationId
     * @return void
     */
    public function __construct($segmentName, SegmentInterface $segment, $notificationId)
    {
        if(in_array($segmentName, static::$segmentNames)) {
            $this->segmentName = $segmentName;
        } else {
            throw new \Exception('Invalid Segment Name: '.$segmentName);
        }
        
        $this->segment = $segment;
        $this->notificationId = $notificationId;
    }
    
    /**
     * Convert all segment dates to Y-m-d format.
     *
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (array) $rawAttributes
     * @param (array) $values
     * @return array $values
     */
    public function convertDates(SegmentInterface $segment, $rawAttributes, $values)
    {
        foreach ($segment::__callStatic('getDates', []) as $date) {
            if (!empty($values[$date])) {
                $dt = new \DateTime($rawAttributes[$date]);
                $values[$date] = $dt->format('Y-m-d');
            }
        }
        
        return $values;
    }
    
    /**
     * Remove empty strings and null values.
     *
     * @param (array) $data
     * @return array $data
     */
    public function cleanData($data)
    {
        if (count($data)) {
            foreach ($data as $k => $v) {
                if(!mb_strlen(trim($v))) {
                    unset($data[$k]); 
                }
            }
        }
        
        return $data;
    }
    
    /**
     * A test to see if this profile should be used.
     * It could be given a form request, a segment, or a notification.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (string) $notificationId
     * @return boolean
     */
    public abstract static function useThisProfile($segmentName, SegmentInterface $segment, $notificationId);
    
    /**
     * Get the profile name, mainly used for debugging.
     *
     * @return void
     */
    public function getProfileName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
    
    /**
     * Does the profile have a misc segment.
     *
     * @return boolean
     */
    public function hasMiscSegment()
    {
        return $this->Misc_Segment_isPresent();
    }
    
    /**
     * Get the misc segment name.
     *
     * @return string or null
     */
    public function getMiscSegmentName()
    {
        return $this->Misc_Segment_getName();
    }
    
    /**
     * Is the segment mandatory.
     *
     * @return boolean
     */
    public function isMandatory()
    {
        $functionName = $this->segmentName.'_isMandatory';
        
        return $this->$functionName();
    }
    
    /**
     * Get an array of segment form inputs.
     *
     * @return array
     */
    public function getFormInputs()
    {
        $functionName = $this->segmentName.'_getFormInputs';
        
        return $this->$functionName();
    }
    
    /**
     * Get an array of segment validation rules.
     *
     * @param (int) $id
     * @return array
     */
    public function getValidationRules($id)
    {
        $functionName = $this->segmentName.'_getValidationRules';
        
        return $this->$functionName($id);
    }
    
    /**
     * Get an array of form attributes for validation error output.
     *
     * @return array
     */
    public function getFormAttributes()
    {
        $functionName = $this->segmentName.'_getFormAttributes';
        
        return $this->$functionName();
    }
    
    /**
     * Get an array of custom validation messages.
     * If there are no custom messages required it can return an empty array.
     *
     * @return array
     */
    public function getValidationMessages()
    {
        $functionName = $this->segmentName.'_getValidationMessages';
        
        if (method_exists($this, $functionName)) {
            return $this->$functionName();
        }
        
        return [];
    }
    
    /**
     * Perform any conditional validation.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return \Illuminate\Validation\Validator  $validator
     */
    public function conditionalValidation(Validator $validator)
    {
        $functionName = $this->segmentName.'_conditionalValidation';
        
        if (method_exists($this, $functionName)) {
            return $this->$functionName($validator);
        }
        
        return $validator;
    }
    
    /**
     * Get the segment data.
     *
     * @return array
     */
    public function export()
    {
        $functionName = $this->segmentName.'_export';
        
        if (method_exists($this, $functionName)) {
            return $this->$functionName();
        }
        
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
    
    /**
     |-------------
     | HDR_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function HDR_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function HDR_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function HDR_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function HDR_Segment_getFormAttributes();
    
    /**
     |-------------
     | AID_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function AID_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function AID_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function AID_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function AID_Segment_getFormAttributes();
    
    /**
     |-------------
     | EID_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function EID_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function EID_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function EID_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function EID_Segment_getFormAttributes();
    
    /**
     |-------------
     | API_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function API_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function API_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function API_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function API_Segment_getFormAttributes();
    
    /**
     |-------------
     | RCS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function RCS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function RCS_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function RCS_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function RCS_Segment_getFormAttributes();
    
    /**
     |-------------
     | SAS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function SAS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function SAS_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function SAS_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function SAS_Segment_getFormAttributes();
    
    /**
     |-------------
     | SUS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function SUS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function SUS_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function SUS_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function SUS_Segment_getFormAttributes();
    
    /**
     |-------------
     | RLS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function RLS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function RLS_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function RLS_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function RLS_Segment_getFormAttributes();
    
    /**
     |-------------
     | LNK_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function LNK_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function LNK_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function LNK_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function LNK_Segment_getFormAttributes();
    
    /**
     |-------------
     | ATT_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function ATT_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function ATT_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function ATT_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function ATT_Segment_getFormAttributes();
    
    /**
     |-------------
     | SPT_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function SPT_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function SPT_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function SPT_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function SPT_Segment_getFormAttributes();
    
    /**
     |-------------
     | WPS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function WPS_Segment_isMandatory();
	
	/**
     * Get the segment form input array.
     *
     * @return array
     */
	protected abstract function WPS_Segment_getFormInputs();
	
	/**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
	protected abstract function WPS_Segment_getValidationRules($id = NULL);
	
	/**
     * Get the segment form attributes array.
     *
     * @return array
     */
	protected abstract function WPS_Segment_getFormAttributes();
    
    /**
     |-------------
     | NHS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function NHS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function NHS_Segment_getFormInputs();
	
	/**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
	protected abstract function NHS_Segment_getValidationRules($id = NULL);
	
	/**
     * Get the segment form attributes array.
     *
     * @return array
     */
	protected abstract function NHS_Segment_getFormAttributes();
    
    /**
     |-------------
     | RPS_Segment
     |-------------
     */
    
    /**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function RPS_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function RPS_Segment_getFormInputs();
	
	/**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
	protected abstract function RPS_Segment_getValidationRules($id = NULL);
	
	/**
     * Get the segment form attributes array.
     *
     * @return array
     */
	protected abstract function RPS_Segment_getFormAttributes();
	
	/**
     |-------------
     | Misc_Segment
     |-------------
     */
	
	/**
	 * Is the Misc Segment present.
	 *
	 * @return boolean
	 */
	protected abstract function Misc_Segment_isPresent();
	
	/**
	 * Get the name of the Misc Segment.
	 *
	 * @return string
	 */
	protected abstract function Misc_Segment_getName();
	
	/**
     * Is this segment mandatory.
     *
     * @return boolean
     */
    protected abstract function Misc_Segment_isMandatory();
    
    /**
     * Get the segment form input array.
     *
     * @return array
     */
    protected abstract function Misc_Segment_getFormInputs();
    
    /**
     * Get the segment validation rules.
     *
     * @param  integer $id
     * @return array
     */
    protected abstract function Misc_Segment_getValidationRules($id = NULL);
    
    /**
     * Get the segment form attributes array.
     *
     * @return array
     */
    protected abstract function Misc_Segment_getFormAttributes();
}