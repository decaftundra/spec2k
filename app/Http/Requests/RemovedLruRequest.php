<?php

namespace App\Http\Requests;

use App\ShopFindings\RLS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\RLS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class RemovedLruRequest extends SegmentFormRequest implements RLS_SegmentInterface
{
    protected $segmentName = 'RLS_Segment';
    
    /**
     * Get the Removed Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RLS_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Removed Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RLS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Removed Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RLS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Removal Date.
     *
     * @return date
     */
    public function get_RLS_RED()
    {
        return $this->request->has('RED') ? (string) $this->request->get('RED') : NULL;
    }
    
    /**
     * Get the Removal Type Code.
     *
     * @return string
     */
    public function get_RLS_TTY()
    {
        return (string) $this->request->get('TTY');
    }
    
    /**
     * Get the Removal Type Text.
     *
     * @return string
     */
    public function get_RLS_RET()
    {
        return (string) $this->request->get('RET');
    }
    
    /**
     * Get the Install Date of Removed Part.
     *
     * @return date
     */
    public function get_RLS_DOI()
    {
        return $this->request->has('DOI') ? (string) $this->request->get('DOI') : NULL;
    }
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RLS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Removed Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RLS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RLS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RLS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Removal Reason Text.
     *
     * @return string
     */
    public function get_RLS_RMT()
    {
        return (string) $this->request->get('RMT');
    }
    
    /**
     * Get the Engine/APU Position Identifier.
     *
     * @return string
     */
    public function get_RLS_APT()
    {
        return (string) $this->request->get('APT');
    }
    
    /**
     * Get the Part Position Code.
     *
     * @return string
     */
    public function get_RLS_CPI()
    {
        return (string) $this->request->get('CPI');
    }
    
    /**
     * Get the Part Position.
     *
     * @return string
     */
    public function get_RLS_CPT()
    {
        return (string) $this->request->get('CPT');
    }
    
    /**
     * Get the Removed Part Description.
     *
     * @return string
     */
    public function get_RLS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RLS_PML()
    {
        return (string) $this->request->get('PML');
    }
    
    /**
     * Get the Removed Operator Part Number.
     *
     * @return string
     */
    public function get_RLS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Removed Operator Serial Number.
     *
     * @return string
     */
    public function get_RLS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RLS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RLS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    /**
     * Get the Removal Reason Code.
     *
     * @return string
     */
    public function get_RLS_RFR()
    {
        return (string) $this->request->get('RFR');
    }
    
    public function getDates()
    {
        return (new RLS_Segment)->getDates();
    }
}
