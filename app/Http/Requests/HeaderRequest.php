<?php

namespace App\Http\Requests;

use App\HDR_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\HDR_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class HeaderRequest extends SegmentFormRequest implements HDR_SegmentInterface
{
    protected $segmentName = 'HDR_Segment';
    
    /**
     * Get the Change Code.
     *
     * @return string
     */
    public function get_HDR_CHG()
    {
        return (string) $this->request->get('CHG');
    }
    
    /**
     * Get the Reporting Organisation Name.
     *
     * @return string
     */
    public function get_HDR_RON()
    {
        return (string) $this->request->get('RON');
    }
    
    /**
     * Get the Reporting Organisation Cage Code.
     *
     * @return string
     */
    public function get_HDR_ROC()
    {
        return (string) $this->request->get('ROC');
    }
    
    /**
     * Get the Operator Code.
     *
     * @return string
     */
    public function get_HDR_OPR()
    {
        return (string) $this->request->get('OPR');
    }
    
    /**
     * Get the Operator Name.
     *
     * @return string
     */
    public function get_HDR_WHO()
    {
        return $this->request->get('WHO');
    }
    
    /**
     * Get the Reporting Period Start Date.
     *
     * @return date
     */
    public function get_HDR_RDT()
    {
        return (string) $this->request->get('RDT');
    }
    
    /**
     * Get the Reporting Period End Date.
     *
     * @return date
     */
    public function get_HDR_RSD()
    {
        return (string) $this->request->get('RSD');
    }
    
    public function getDates()
    {
        return (new HDR_Segment)->getDates();
    }
}
