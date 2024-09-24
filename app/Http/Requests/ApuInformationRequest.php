<?php

namespace App\Http\Requests;

use App\ShopFindings\API_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\API_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class ApuInformationRequest extends SegmentFormRequest implements API_SegmentInterface
{
    protected $segmentName = 'API_Segment';
    
    /**
     * Get the Aircraft APU Type.
     *
     * @return string
     */
    public function get_API_AET()
    {
        return (string) $this->request->get('AET');
    }
    
    /**
     * Get the APU Serial Number.
     *
     * @return string
     */
    public function get_API_EMS()
    {
        return (string) $this->request->get('EMS');
    }
    
    /**
     * Get the Aircraft APU Model.
     *
     * @return string
     */
    public function get_API_AEM()
    {
        return (string) $this->request->get('AEM');
    }
    
    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_API_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the APU Cumulative Hours.
     *
     * @return float
     */
    public function get_API_ATH()
    {
        return $this->request->has('ATH') ? (float) $this->request->get('ATH') : NULL;
    }
    
    /**
     * Get the APU Cumulative Cycles.
     *
     * @return integer
     */
    public function get_API_ATC()
    {
        return $this->request->has('ATC') ? (int) $this->request->get('ATC') : NULL;
    }
    
    public function getDates()
    {
        return (new APU_Segment)->getDates();
    }
}
