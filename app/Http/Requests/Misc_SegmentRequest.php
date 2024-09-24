<?php

namespace App\Http\Requests;

use App\ShopFindings\Misc_Segment;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\SegmentFormRequest;

class Misc_SegmentRequest extends SegmentFormRequest
{
    protected $segmentName = 'Misc_Segment';
    
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
    
    public function getDates()
    {
        return (new Misc_Segment)->getDates();
    }
}
