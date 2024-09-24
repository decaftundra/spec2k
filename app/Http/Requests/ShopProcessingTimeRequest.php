<?php

namespace App\Http\Requests;

use App\ShopFindings\SPT_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\SPT_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class ShopProcessingTimeRequest extends SegmentFormRequest implements SPT_SegmentInterface
{
    protected $segmentName = 'SPT_Segment';
    
    /**
     * Get the Shop Total Labor Hours.
     *
     * @return float
     */
    public function get_SPT_MAH()
    {
        return $this->request->has('MAH') ? (float) $this->request->get('MAH') : NULL;
    }
    
    /**
     * Get the Shop Flow Time.
     *
     * @return integer
     */
    public function get_SPT_FLW()
    {
        return $this->request->has('FLW') ? (int) $this->request->get('FLW') : NULL;
    }
    
    /**
     * Get the Shop Turn Around Time.
     *
     * @return integer
     */
    public function get_SPT_MST()
    {
        return $this->request->has('MST') ? (int) $this->request->get('MST') : NULL;
    }
    
    public function getDates()
    {
        return (new SPT_Segment)->getDates();
    }
}
