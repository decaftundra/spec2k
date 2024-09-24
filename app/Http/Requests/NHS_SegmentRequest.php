<?php

namespace App\Http\Requests;

use App\PieceParts\NHS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\NHS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class NHS_SegmentRequest extends SegmentFormRequest implements NHS_SegmentInterface
{
    protected $segmentName = 'NHS_Segment';
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Code.
     *
     * @return string
     */
    public function get_NHS_MFR()
    {
        return in_array($this->MFR, ['ZZZZZ', 'zzzzz']) ? '' : (string) $this->MFR;
    }
    
    /**
     * Get the Next Higher Assembly Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_NHS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Serial Number.
     *
     * @return string
     */
    public function get_NHS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Name.
     *
     * @return string
     */
    public function get_NHS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Number.
     *
     * @return string
     */
    public function get_NHS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_NHS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Failed Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_NHS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Name.
     *
     * @return string
     */
    public function get_NHS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Part Number.
     *
     * @return string
     */
    public function get_NHS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Serial Number.
     *
     * @return string
     */
    public function get_NHS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_NHS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Failed Piece Part NHA Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_NHS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly NHA Part Number.
     *
     * @return string
     */
    public function get_NHS_NPN()
    {
        return (string) $this->request->get('NPN');
    }
    
    public function getDates()
    {
        return (new NHS_Segment)->getDates();
    }
}
