<?php

namespace App\Http\Requests;

use App\ShopFindings\RCS_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class ReceivedLruRequest extends SegmentFormRequest implements RCS_SegmentInterface
{
    protected $segmentName = 'RCS_Segment';
    protected $ignoreParameter = 'SFI';
    
    /**
     * Get the Shop Findings Record Identifier.
     *
     * @return string
     */
    public function get_RCS_SFI()
    {
        return (string) $this->request->get('SFI');
    }
    
    /**
     * Get the Shop Received Date .
     *
     * @return date
     */
    public function get_RCS_MRD()
    {
        return (string) $this->request->get('MRD');
    }
    
    /**
     * Get the Received Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RCS_MFR()
    {
        return (string) $this->request->get('MFR');
    }
    
    /**
     * Get the Received Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RCS_MPN()
    {
        return (string) $this->request->get('MPN');
    }
    
    /**
     * Get the Received Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RCS_SER()
    {
        return (string) $this->request->get('SER');
    }
    
    /**
     * Get the Supplier Removal Type Code.
     *
     * @return string
     */
    public function get_RCS_RRC()
    {
        return (string) $this->request->get('RRC');
    }
    
    /**
     * Get the Failure/Fault Found.
     *
     * @return string
     */
    public function get_RCS_FFC()
    {
        return (string) $this->request->get('FFC');
    }
    
    /**
     * Get the Failure/Fault Induced.
     *
     * @return string
     */
    public function get_RCS_FFI()
    {
        return (string) $this->request->get('FFI');
    }
    
    /**
     * Get the Failure/Fault Confirms Reason For Removal.
     *
     * @return string
     */
    public function get_RCS_FCR()
    {
        return (string) $this->request->get('FCR');
    }
    
    /**
     * Get the Failure/Fault Confirms Aircraft Message.
     *
     * @return string
     */
    public function get_RCS_FAC()
    {
        return (string) $this->request->get('FAC');
    }
    
    /**
     * Get the Failure/Fault Confirms Aircraft Part Bite Message.
     *
     * @return string
     */
    public function get_RCS_FBC()
    {
        return (string) $this->request->get('FBC');
    }
    
    /**
     * Get the Hardware/Software Failure.
     *
     * @return string
     */
    public function get_RCS_FHS()
    {
        return (string) $this->request->get('FHS');
    }
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RCS_MFN()
    {
        return (string) $this->request->get('MFN');
    }
    
    /**
     * Get the Received Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RCS_PNR()
    {
        return (string) $this->request->get('PNR');
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RCS_OPN()
    {
        return (string) $this->request->get('OPN');
    }
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RCS_USN()
    {
        return (string) $this->request->get('USN');
    }
    
    /**
     * Get the Supplier Removal Type Text.
     *
     * @return string
     */
    public function get_RCS_RET()
    {
        return (string) $this->request->get('RET');
    }
    
    /**
     * Get the Customer Code.
     *
     * @return string
     */
    public function get_RCS_CIC()
    {
        return (string) $this->request->get('CIC');
    }
    
    /**
     * Get the Repair Order Identifier.
     *
     * @return string
     */
    public function get_RCS_CPO()
    {
        return (string) $this->request->get('CPO');
    }
    
    /**
     * Get the Packing Sheet Number.
     *
     * @return string
     */
    public function get_RCS_PSN()
    {
        return (string) $this->request->get('PSN');
    }
    
    /**
     * Get the Work Order Number.
     *
     * @return string
     */
    public function get_RCS_WON()
    {
        return (string) $this->request->get('WON');
    }
    
    /**
     * Get the Maintenance Release Authorization Number.
     *
     * @return string
     */
    public function get_RCS_MRN()
    {
        return (string) $this->request->get('MRN');
    }
    
    /**
     * Get the Contract Number.
     *
     * @return string
     */
    public function get_RCS_CTN()
    {
        return (string) $this->request->get('CTN');
    }
    
    /**
     * Get the Master Carton Number.
     *
     * @return string
     */
    public function get_RCS_BOX()
    {
        return (string) $this->request->get('BOX');
    }
    
    /**
     * Get the Received Operator Part Number.
     *
     * @return string
     */
    public function get_RCS_ASN()
    {
        return (string) $this->request->get('ASN');
    }
    
    /**
     * Get the Received Operator Serial Number.
     *
     * @return string
     */
    public function get_RCS_UCN()
    {
        return (string) $this->request->get('UCN');
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RCS_SPL()
    {
        return (string) $this->request->get('SPL');
    }
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RCS_UST()
    {
        return (string) $this->request->get('UST');
    }
    
    /**
     * Get the Manufacturer Part Description.
     *
     * @return string
     */
    public function get_RCS_PDT()
    {
        return (string) $this->request->get('PDT');
    }
    
    /**
     * Get the Removed Part Modificiation Level.
     *
     * @return string
     */
    public function get_RCS_PML()
    {
        return (string) $this->request->get('PML');
    }
    
    /**
     * Get the Shop Findings Code.
     *
     * @return string
     */
    public function get_RCS_SFC()
    {
        return (string) $this->request->get('SFC');
    }
    
    /**
     * Get the Related Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_RCS_RSI()
    {
        return (string) $this->request->get('RSI');
    }
    
    /**
     * Get the Repair Location Name.
     *
     * @return string
     */
    public function get_RCS_RLN()
    {
        return (string) $this->request->get('RLN');
    }
    
    /**
     * Get the Incoming Inspection Text.
     *
     * @return string
     */
    public function get_RCS_INT()
    {
        return (string) $this->request->get('INT');
    }
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_RCS_REM()
    {
        return (string) $this->request->get('REM');
    }
    
    public function getDates()
    {
        return (new RCS_Segment)->getDates();
    }
}
