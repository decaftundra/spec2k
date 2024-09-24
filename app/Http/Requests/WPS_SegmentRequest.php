<?php

namespace App\Http\Requests;

use App\PieceParts\WPS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\WPS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class WPS_SegmentRequest extends SegmentFormRequest implements WPS_SegmentInterface
{
    protected $segmentName = 'WPS_Segment';
    protected $ignoreParameter = 'PPI';
    
    /**
     * Get the Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_WPS_SFI()
    {
        return (string) $this->request->get('SFI');
    }
    
    /**
     * Get the Piece Part Record Identifier.
     *
     * @return string
     */
    public function get_WPS_PPI()
    {
        return (string) $this->request->get('PPI');
    }
    
    /**
     * Get the Primary Piece Part Failure Indicator.
     *
     * @return string
     */
    public function get_WPS_PFC()
    {
        return (string) $this->request->get('PFC');
    }
    
    /**
     * Get the Failed Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_WPS_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Failed Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_WPS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Failed Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_WPS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Failed Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Piece Part Failure Description.
     *
     * @return string
     */
    public function get_WPS_FDE()
    {
        return (string) $this->request->get('FDE');
    }
    
    /**
     * Get the Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_WPS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_WPS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Failed Piece Part Description.
     *
     * @return string
     */
    public function get_WPS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    /**
     * Get the Piece Part Reference Designator Symbol.
     *
     * @return string
     */
    public function get_WPS_GEL()
    {
        return (string) $this->request->get('GEL');
    }
    
    /**
     * Get the Received Date.
     *
     * @return date
     */
    public function get_WPS_MRD()
    {
        return $this->request->has('MRD') ? $this->request->get('MRD') : NULL;
    }
    
    /**
     * Get the Operator Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_WPS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_WPS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    public function getDates()
    {
        return (new WPS_Segment)->getDates();
    }
}
