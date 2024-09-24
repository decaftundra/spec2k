<?php

namespace App\Http\Requests;

use App\PieceParts\RPS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\RPS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class RPS_SegmentRequest extends SegmentFormRequest implements RPS_SegmentInterface
{
    protected $segmentName = 'RPS_Segment';
    
    /**
     * Get the Replaced Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RPS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Replaced Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_RPS_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Replaced Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_RPS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Replaced Vendor Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Replaced Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RPS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_RPS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Replaced Operator Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Replaced Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RPS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RPS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    /**
     * Get the Replaced Vendor Piece Part Description.
     *
     * @return string
     */
    public function get_RPS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    public function getDates()
    {
        return (new RPS_Segment)->getDates();
    }
}
