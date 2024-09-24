<?php

namespace App\Http\Requests;

use App\ShopFindings\SUS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\SUS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class ShippedLruRequest extends SegmentFormRequest implements SUS_SegmentInterface
{
    protected $segmentName = 'SUS_Segment';
    
    /**
     * Get the Shipped Date.
     *
     * @return date
     */
    public function get_SUS_SHD()
    {
        return $this->request->has('SHD') ? (string) $this->request->get('SHD') : NULL;
    }
    
    /**
     * Get the Shipped Part Manufacturer Code.
     *
     * @return string
     */
    public function get_SUS_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Shipped Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_SUS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Shipped Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_SUS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Shipped Part Manufacturer Name.
     *
     * @return string
     */
    public function get_SUS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Shipped Manufacturer Part Description.
     *
     * @return string
     */
    public function get_SUS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    /**
     * Get the Shipped Manufacturer Part Number.
     *
     * @return string
     */
    public function get_SUS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_SUS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Shipped Universal Serial Number.
     *
     * @return string
     */
    public function get_SUS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Shipped Operator Part Number.
     *
     * @return string
     */
    public function get_SUS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Shipped Operator Serial Number.
     *
     * @return string
     */
    public function get_SUS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_SUS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Shipped Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_SUS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    /**
     * Get the Shipped Part Modification Level.
     *
     * @return string
     */
    public function get_SUS_PML()
    {
        return (string) $this->request->get('PML');
    }
    
    /**
     * Get the Shipped Part Status Code.
     *
     * @return string
     */
    public function get_SUS_PSC()
    {
        return (string) $this->request->get('PSC');
    }
    
    public function getDates()
    {
        return (new SUS_Segment)->getDates();
    }
}
