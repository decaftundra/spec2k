<?php

namespace App;

use App\Interfaces\SegmentInterface;
use \Illuminate\Validation\Validator;
use App\ValidationProfiles\DefaultProfile;

class ValidationProfiler
{
    protected $profile;
    
    /**
     * Build the profiler.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (string) $notificationId
     * @return void
     */
    public function __construct($segmentName, SegmentInterface $segment, $notificationId)
    {
        if (is_null($notificationId)) {
            throw new \Exception('Notification ID cannot be null.');
        }
        
        $this->setProfile($segmentName, $segment, $notificationId);
    }
    
    /**
     * Loops through all validation profiles to find the correct one.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (int) $notificationId
     * @return void
     */
    private function setProfile($segmentName, SegmentInterface $segment, $notificationId)
    {
        $compatibleProfiles = 0;
        
        $profiles = config('validation_profiles.profiles', []);
        
        foreach ($profiles as $profile) {
            if ($profile::useThisProfile($segmentName, $segment, $notificationId)) {
                $compatibleProfiles++;
                $this->profile = new $profile($segmentName, $segment, $notificationId);
            }
        }
        
        if (is_array($compatibleProfiles) && count($compatibleProfiles) > 1) {
            throw new \Exception('Validation profile conflicts. '. json_encode($this->compatibleProfiles));
        }
        
        if (!$this->profile) {
            // Or default to the default profile.
            $this->profile = new DefaultProfile($segmentName, $segment, $notificationId);
        }
    }
    
    /**
     * Get the profile name.
     *
     * @return string
     */
    public function getProfileName()
    {
        return $this->profile->getProfileName();
    }
    
    /**
     * Has the profile got a Misc Segment.
     *
     * @return boolean
     */
    public function hasMiscSegment()
    {
        return $this->profile->hasMiscSegment();
    }
    
    /**
     * Get the Misc Segment name.
     *
     * @return string
     */
    public function getMiscSegmentName()
    {
        return $this->profile->getMiscSegmentName();
    }
    
    /**
     * Is the segment mandatory.
     *
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->profile->isMandatory();
    }
    
    /**
     * Get the form inputs.
     *
     * @return array
     */
    public function getFormInputs()
    {
        return $this->profile->getFormInputs();
    }
    
    /**
     * Get the form attributes.
     *
     * @return array
     */
    public function getFormAttributes()
    {
        return $this->profile->getFormAttributes();
    }
    
    /**
     * Get the validation rules.
     *
     * @param (int) $id
     * @return array
     */
    public function getValidationRules($id = NULL)
    {
        return $this->profile->getValidationRules($id);
    }
    
    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function getValidationMessages()
    {
        return $this->profile->getValidationMessages();
    }
    
    /**
     * Get conditional validation results.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return ???
     */
    public function conditionalValidation(Validator $validator)
    {
        return $this->profile->conditionalValidation($validator);
    }
    
    /**
     * ??? Possibly not used.
     *
     * @return
     */
    public function export()
    {
        return $this->profile->export();
    }
}