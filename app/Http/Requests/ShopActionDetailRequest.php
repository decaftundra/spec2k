<?php

namespace App\Http\Requests;

use App\ShopFindings\SAS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\SAS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class ShopActionDetailRequest extends SegmentFormRequest implements SAS_SegmentInterface
{
    protected $segmentName = 'SAS_Segment';
    
    /**
     * Get the Shop Action Text Incoming.
     *
     * @return string
     */
    public function get_SAS_INT()
    {
        return (string) $this->request->get('INT');
    }
    
    /**
     * Get the Shop Repair Location Code.
     *
     * @return string
     */
    public function get_SAS_SHL()
    {
        return (string) $this->request->get('SHL');
    }
    
    /**
     * Get the Shop Final Action Indicator.
     *
     * @return should be boolean but will be a string (see comment)
     */
    public function get_SAS_RFI()
    {
        return $this->request->get('RFI');
    }
    
    /**
     * Get the Mod (S) Incorporated (This Visit) Text.
     *
     * @return string
     */
    public function get_SAS_MAT()
    {
        return (string) $this->request->get('MAT');
    }
    
    /**
     * Get the Shop Action Code.
     *
     * @return string
     */
    public function get_SAS_SAC()
    {
        return (string) $this->request->get('SAC');
    }
    
    /**
     * Get the Shop Disclosure Indicator.
     *
     * @return should be boolean but will be a string (see comment)
     */
    public function get_SAS_SDI()
    {
        return $this->request->get('SDI');
    }
    
    /**
     * Get the Part Status Code.
     *
     * @return string
     */
    public function get_SAS_PSC()
    {
        return (string) $this->request->get('PSC');
    }
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_SAS_REM()
    {
        return (string) $this->request->get('REM');
    }
    
    public function getDates()
    {
        return (new SAS_Segment)->getDates();
    }
}
