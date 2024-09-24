<?php

namespace App\Http\Requests;

use App\ShopFindings\AID_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\AID_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class AirframeInformationRequest extends SegmentFormRequest implements AID_SegmentInterface
{
    protected $segmentName = 'AID_Segment';
    
    /**
     * Get the Airframe Manufacturer Code.
     *
     * @return string
     */
    public function get_AID_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Aircraft Model.
     *
     * @return string
     */
    public function get_AID_AMC()
    {
        return (string) $this->request->get('AMC');
    }
    
    /**
     * Get the Airframe Manufacturer Name.
     *
     * @return string
     */
    public function get_AID_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Aircraft Series.
     *
     * @return string
     */
    public function get_AID_ASE()
    {
        return (string) $this->request->get('ASE');
    }
    
    /**
     * Get the Aircraft Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_AID_AIN()
    {
        return (string) $this->request->get('AIN');
    }
    
    /**
     * Get the Aircraft Registration Number.
     *
     * @return string
     */
    public function get_AID_REG()
    {
        return (string) $this->request->get('REG');
    }
    
    /**
     * Get the Operator Aircraft Internal Identifier.
     *
     * @return string
     */
    public function get_AID_OIN()
    {
        return (string) $this->request->get('OIN');
    }
    
    /**
     * Get the Aircraft Cumulative Total Flight Hours.
     *
     * @return float
     */
    public function get_AID_CTH()
    {
        return $this->request->has('CTH') ? (float) $this->request->get('CTH') : NULL;
    }
    
    /**
     * Get the Aircraft Cumulative Total Cycles.
     *
     * @return integer
     */
    public function get_AID_CTY()
    {
        return $this->request->has('CTY') ? (int) $this->request->get('CTY') : NULL;
    }
    
    public function getDates()
    {
        return (new AID_Segment)->getDates();
    }
}
