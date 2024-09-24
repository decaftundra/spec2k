<?php

namespace App\Http\Requests;

use App\ShopFindings\ATT_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\ATT_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class AccumulatedTimeTextRequest extends SegmentFormRequest implements ATT_SegmentInterface
{
    protected $segmentName = 'ATT_Segment';
    
    /**
     * Get the Time/Cycle Reference Code.
     *
     * @return string
     */
    public function get_ATT_TRF()
    {
        return (string) $this->request->get('TRF');
    }
    
    /**
     * Get the Operating Time.
     *
     * @return integer
     */
    public function get_ATT_OTT()
    {
        return $this->request->has('OTT') ? (int) $this->request->get('OTT') : NULL;
    }
    
    /**
     * Get the Operating Cycle Count.
     *
     * @return integer
     */
    public function get_ATT_OPC()
    {
        return $this->request->has('OPC') ? (int) $this->request->get('OPC') : NULL;
    }
    
    /**
     * Get the Operating Day Count.
     *
     * @return integer
     */
    public function get_ATT_ODT()
    {
        return $this->request->has('ODT') ? (int) $this->request->get('ODT') : NULL;
    }
    
    public function getDates()
    {
        return (new ATT_Segment)->getDates();
    }
}
