<?php

namespace App\ValidationProfiles;

use App\CageCode;
use App\UtasCode;
use App\Notification;
use App\Codes\Airline;
use App\UtasPartNumber;
use App\Codes\PlantCode;
use App\Rules\Lowercase;
use App\Rules\Uppercase;
use App\Codes\FaultFoundCode;
use App\Codes\PartStatusCode;
use App\Codes\ShopActionCode;
use Illuminate\Validation\Rule;
use App\Codes\FaultInducedCode;
use App\ShopFindings\ShopFinding;
use App\Rules\FirstLetterUppercase;
use App\Interfaces\SegmentInterface;
use App\Codes\ShopRepairFacilityCode;
use Illuminate\Support\Facades\Cache;
use App\Codes\SupplierRemovalTypeCode;
use App\Codes\HardwareSoftwareFailureCode;
use App\ValidationProfiles\DefaultProfile;
use App\Codes\FaultConfirmsAircraftMessageCode;
use App\Codes\FaultConfirmsReasonForRemovalCode;
use App\Codes\FaultConfirmsAircraftPartBiteMessageCode;

/**
 * Note: Utas have now been bought out by Collins.
 */

class UtasProfile extends DefaultProfile
{
    /**
     * The misc segment name.
     *
     * @constant string
     */
    const MISC_SEGMENT_NAME = 'Collins Fields';
    
    /**
     * A test to see if this profile should be used.
     * It could be given a form request, a segment, or a notification.
     *
     * @return boolean
     */
    public static function useThisProfile($segmentName, SegmentInterface $segment, $notificationId)
    {
        // Search on segment first.
        if (method_exists($segment, 'get_RCS_MPN')) {
            $partNo = $segment->get_RCS_MPN();
        } else {
            // Try to find a saved record next.
            $shopFinding = ShopFinding::whereHas('ShopFindingsDetail.RCS_Segment', function($query) use ($notificationId) {
                $query->whereNotNull('id');
            })->find($notificationId);
            
            if ($shopFinding && $shopFinding->ShopFindingsDetail->RCS_Segment) {
                $partNo = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN();
            } else {
                if (Cache::has('notification.'.$notificationId)) {
                    $notification = Cache::get('notification.'.$notificationId);
                } else {
                    $notification = Notification::where('rcsSFI', $notificationId)->first();
                    
                    Cache::put('notification.'.$notificationId, $notification, 3660);
                }
                
                if (!$notification) return false;
                
                $partNo = $notification->get_RCS_MPN();
            }
        }
        
        if (!$partNo) return false;
        
        return in_array($partNo, UtasCode::getAllUtasCodes());
    }
    
    /**
     |-------------
     | RCS_Segment
     |-------------
     */
    
    protected function RCS_Segment_isMandatory()
    {
        return true;
    }
    
    protected function RCS_Segment_getFormInputs()
    {
        $array = [
            'SFI' => [
                'title'         => 'Shop Findings Record Identifier',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 50,
                'function'      => 'get_RCS_SFI',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 10,
                'display'       => true
            ],
        	'MRD' => [
            	'title'         => 'Material Receipt Date',
                'required'      => true,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'function'      => 'get_RCS_MRD',
                'input_width'    => 'col-sm-6 col-md-6',
                'order'         => 20,
                'display'       => true,
                'admin_only'    => true
            ],
        	'MFR' => [
            	'title'         => 'Manufacturer Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RCS_MFR',
                'input_width'    => 'col-sm-6 col-md-6',
                'order'         => 30,
                'display'       => true,
                'description'   => 'Use "ZZZZZ" if unavailable.'
            ],
        	'MPN' => [
            	'title'         => 'Manufacturer Full Length Part No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RCS_MPN',
                'input_width'    => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true,
                'admin_only'    => true
            ],
        	'SER' => [
            	'title'         => 'Part Serial No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RCS_SER',
                'input_width'    => 'col-sm-6 col-md-6',
                'order'         => 50,
                'display'       => true,
                'admin_only'    => true
            ],
        	'RRC' => [
            	'title'         => 'Supplier Removal Type Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1,
                'options'       => SupplierRemovalTypeCode::getDropDownValues(),
                'function'      => 'get_RCS_RRC',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 60,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FFC' => [
            	'title'         => 'Failure/Fault Found Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => FaultFoundCode::getDropDownValues(),
                'function'      => 'get_RCS_FFC',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 70,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FFI' => [
            	'title'         => 'Failure/Fault Induced Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => FaultInducedCode::getDropDownValues(),
                'function'      => 'get_RCS_FFI',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 80,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FCR' => [
            	'title'         => 'Failure/Fault Confirm Reason Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => FaultConfirmsReasonForRemovalCode::getDropDownValues(),
                'function'      => 'get_RCS_FCR',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 90,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FAC' => [
            	'title'         => 'Failure/Fault Confirm Aircraft Message Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => FaultConfirmsAircraftMessageCode::getDropDownValues(),
                'function'      => 'get_RCS_FAC',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 100,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FBC' => [
            	'title'         => 'Failure/Fault Confirm Bite Message Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(),
                'function'      => 'get_RCS_FBC',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 110,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FHS' => [
            	'title'         => 'Hardware/Software Failure Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 2,
                'options'       => HardwareSoftwareFailureCode::getDropDownValues(),
                'function'      => 'get_RCS_FHS',
                'input_width'    => 'col-sm-12 col-md-6',
                'order'         => 85,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'MFN' => [
            	'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'placeholder'   => '',
                'function'      => 'get_RCS_MFN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 130
            ],
        	'PNR' => [
            	'title'         => 'Part Number',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RCS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 140
            ],
        	'OPN' => [
            	'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_RCS_OPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 150
            ],
        	'USN' => [
            	'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 35,
                'function'      => 'get_RCS_USN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 160
            ],
        	'RET' => [
            	'title'         => 'Reason for Removal Clarification Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 64,
                'function'      => 'get_RCS_RET',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 170
            ],
        	'CIC' => [
            	'title'         => 'Customer Identification Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 3,
                'max'           => 5,
                'function'      => 'get_RCS_CIC',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 180
            ],
        	'CPO' => [
            	'title'         => 'Customer Order No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 11,
                'placeholder'   => '',
                'function'      => 'get_RCS_CPO',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 190,
                'display'       => true
            ],
        	'PSN' => [
            	'title'         => 'Packing Sheet No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'placeholder'   => '',
                'function'      => 'get_RCS_PSN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 200
            ],
        	'WON' => [
            	'title'         => 'Work Order No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => '',
                'function'      => 'get_RCS_WON',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 210
            ],
        	'MRN' => [
            	'title'         => 'Maintenance Release Auth No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'placeholder'   => '',
                'function'      => 'get_RCS_MRN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 220
            ],
        	'CTN' => [
            	'title'         => 'Contract No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 4,
                'max'           => 15,
                'placeholder'   => '',
                'function'      => 'get_RCS_CTN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 230
            ],
        	'BOX' => [
            	'title'         => 'Master Carton No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 10,
                'placeholder'   => '',
                'function'      => 'get_RCS_BOX',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 240
            ],
        	'ASN' => [
            	'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RCS_ASN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 250
            ],
        	'UCN' => [
            	'title'         => 'Unique Component Identification No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RCS_UCN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 260
            ],
        	'SPL' => [
            	'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RCS_SPL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 270
            ],
        	'UST' => [
            	'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_RCS_UST',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 280
            ],
        	'PDT' => [
            	'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_RCS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 290
            ],
        	'PML' => [
            	'title'         => 'Part Modification Level',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_RCS_PML',
                'input_width'   => 'col-sm-12 col-md-6',
                'display'       => true,
                'order'         => 133
            ],
        	'SFC' => [
            	'title'         => 'Shop Findings Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 10,
                'function'      => 'get_RCS_SFC',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 320
            ],
        	'RSI' => [
            	'title'         => 'Related Shop Findings Record Identifier',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 50,
                'function'      => 'get_RCS_RSI',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 310
            ],
        	'RLN' => [
            	'title'         => 'Repair Location Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 25,
                'function'      => 'get_RCS_RLN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 330
            ],
        	'INT' => [
            	'title'         => 'Incoming Inspection Text',
            	'display'       => true,
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 5000,
                'function'      => 'get_RCS_INT',
                'input_width'   => 'col-sm-12 col-md-6',
                'order'         => 136
            ],
        ];
        
        uasort($array, [static::class, 'orderFormInputs']);
        
        return $array;
    }
    
    protected function RCS_Segment_getValidationRules($id = NULL)
    {
        return [
            'SFI' => ['required', 'string', 'min:1', 'max:50', Rule::unique('RCS_Segments')->ignore($id, 'SFI')],
        	'MRD' => 'required|date_format:d/m/Y',
        	'MFR' => ['required', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
        	'MPN' => 'required|string|min:1|max:32',
        	'SER' => 'required|string|min:1|max:15',
        	'RRC' => ['required', 'string', 'min:1', 'max:1', Rule::in(SupplierRemovalTypeCode::getPermittedValues())],
        	'FFC' => ['required', 'string', 'min:1', 'max:2', Rule::in(FaultFoundCode::getPermittedValues())],
        	'FFI' => ['required', 'string', 'min:1', 'max:2', Rule::in(FaultInducedCode::getPermittedValues())],
        	'FCR' => ['required', 'string', 'min:1', 'max:2', Rule::in(FaultConfirmsReasonForRemovalCode::getPermittedValues())],
        	'FAC' => ['required', 'string', 'min:1', 'max:2', Rule::in(FaultConfirmsAircraftMessageCode::getPermittedValues())],
        	'FBC' => ['required', 'string', 'min:1', 'max:2', Rule::in(FaultConfirmsAircraftPartBiteMessageCode::getPermittedValues())],
        	'FHS' => ['required', 'string', 'min:1', 'max:2', Rule::in(HardwareSoftwareFailureCode::getPermittedValues())],
        	'MFN' => 'bail|required_if:MFR,ZZZZZ,zzzzz|nullable|string|min:1|max:55',
        	'PNR' => 'nullable|string|min:1|max:15',
        	'OPN' => 'nullable|string|min:16|max:32',
        	'USN' => 'nullable|string|min:6|max:35',
        	'RET' => 'nullable|string|min:1|max:64',
        	'CIC' => 'nullable|string|min:3|max:5',
        	'CPO' => 'nullable|string|min:1|max:11',
        	'PSN' => 'nullable|string|min:1|max:15',
        	'WON' => 'nullable|string|min:1|max:20',
        	'MRN' => 'nullable|string|min:1|max:32',
        	'CTN' => 'nullable|string|min:4|max:15',
        	'BOX' => 'nullable|string|min:1|max:10',
        	'ASN' => 'nullable|string|min:1|max:32',
        	'UCN' => 'nullable|string|min:1|max:15',
        	'SPL' => 'nullable|string|min:5|max:5',
        	'UST' => 'nullable|string|min:6|max:20',
        	'PDT' => 'nullable|string|min:1|max:100',
        	'PML' => 'nullable|string|min:1|max:1000',
        	'SFC' => 'nullable|string|min:1|max:10',
        	'RSI' => 'nullable|string|min:1|max:50',
        	'RLN' => 'nullable|string|min:1|max:25',
        	'INT' => 'nullable|string|min:1|max:5000'
        ];
    }
    
    protected function RCS_Segment_getFormAttributes()
    {
        return [
            'SFI' => 'Notification', // Unique ID
            'MRD' => 'Material Receipt Date', // d/m/y
            'MFR' => 'Received Part Mfg. Code',
            'MPN' => 'Rcvd Part No.',
            'SER' => 'Received Mfg. Serial No.',
            'RRC' => 'Supplier Removal Type Code',
            'FFC' => 'Failure/Fault Found',
            'FFI' => 'Failure/Fault Induced',
            'FCR' => 'Failure/Fault Confirms Reason For Removal',
            'FAC' => 'Failure/Fault Confirms Aircraft Msg',
            'FBC' => 'Failure/Fault Confirms Aircraft Part Bite Msg',
            'FHS' => 'Hardware/Software Failure',
            'MFN' => 'Manufacturer Name'
        ];
    }
    
    /**
     * Export the RCS_Segment data.
     * Overwrite INT value with UTAS Reason.
     *
     * @return array
     */
    protected function RCS_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        $shopFinding = ShopFinding::with('ShopFindingsDetail.Misc_Segment')->find($this->notificationId);
        
        $Misc_Segment = $shopFinding->ShopFindingsDetail->Misc_Segment ?? NULL;
        
        if ($Misc_Segment) {
            $valuesJson = $Misc_Segment->values;
            $valuesObj = json_decode($valuesJson);
            $array['REM'] = $valuesObj->Reason ?? NULL;
        }
        
        // Replace with Utas/Collins Cage Code.
        $array['MFR'] = UtasCode::CAGE_CODE;
        
        // Replace part numbers.
        if (!empty($array['MPN'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['MPN']);
            $array['MPN'] = $utasPartNo ?: $array['MPN'];
        }
        
        if (!empty($array['PNR'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['PNR']);
            $array['PNR'] = $utasPartNo ?: $array['PNR'];
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
    
    /**
     |-------------
     | RLS_Segment
     |-------------
     */
     
    /**
     * Export the RLS_Segment data.
     *
     * @return array
     */
    protected function RLS_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        // Replace with Utas/Collins Cage Code.
        $array['MFR'] = UtasCode::CAGE_CODE;
        
        // Replace part numbers.
        if (!empty($array['MPN'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['MPN']);
            $array['MPN'] = $utasPartNo ?: $array['MPN'];
        }
        
        if (!empty($array['PNR'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['PNR']);
            $array['PNR'] = $utasPartNo ?: $array['PNR'];
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
    
    /**
     |-------------
     | SUS_Segment
     |-------------
     */
    
    /**
     * Export the SUS_Segment data.
     *
     * @return array
     */
    protected function SUS_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        // Replace with Utas/Collins Cage Code.
        $array['MFR'] = UtasCode::CAGE_CODE;
        
        // Replace part numbers.
        if (!empty($array['MPN'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['MPN']);
            $array['MPN'] = $utasPartNo ?: $array['MPN'];
        }
        
        if (!empty($array['PNR'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['PNR']);
            $array['PNR'] = $utasPartNo ?: $array['PNR'];
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
    
    /**
     |-------------
     | SAS_Segment
     |-------------
     */
    
    protected function SAS_Segment_isMandatory()
    {
        return true;
    }
    
    protected function SAS_Segment_getFormInputs()
    {
        $array = [
        	'SHL' => [
            	'title'         => 'Shop Repair Facility Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 3,
                'options'       => ShopRepairFacilityCode::getDropDownValues(false),
                'function'      => 'get_SAS_SHL',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true
            ],
        	'RFI' => [
            	'title'         => 'Repair Final Action Indicator',
                'required'      => true,
                'input_type'    => 'radio',
                'data_type'     => 'boolean',
                'function'      => 'get_SAS_RFI',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 60,
                'options'       => [1 => 'Yes', 0 => 'No'],
                'description'   => 'Shop returning the part certified back to service.',
                'display'       => true
            ],
        	'MAT' => [
            	'title'         => 'Manufacturer Authority Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'function'      => 'get_SAS_MAT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 30,
                'display'       => true,
                'description'   => 'MOD(S) Incorporated (This Visit) Text'
            ],
        	'SAC' => [
            	'title'         => 'Shop Action Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 5,
                'options'       => ShopActionCode::getDropDownValues(),
                'function'      => 'get_SAS_SAC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70,
                'display'       => true
            ],
        	'SDI' => [
            	'title'         => 'Shop Disclosure Indicator',
                'required'      => false,
                'input_type'    => 'radio',
                'options'       => [1 => 'Yes', 0 => 'No'],
                'data_type'     => 'boolean',
                'function'      => 'get_SAS_SDI',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70,
                'description'   => 'Technical incidents reported to Supplier\'s (manufacturer) own regulatory agency.'
            ],
        	'PSC' => [
            	'title'         => 'Part Status Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 16,
                'options'       => PartStatusCode::getDropDownValues(),
                'function'      => 'get_SAS_PSC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true
            ],
        	'REM' => [
            	'title'         => 'Remarks Text',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_SAS_REM',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 80
            ]
        ];
        
        uasort($array, [static::class, 'orderFormInputs']);
        
        return $array;
    }
    
    protected function SAS_Segment_getValidationRules($id = NULL)
    {
        return [
            'SHL' => ['required', 'string', 'min:1', 'max:3', Rule::in(ShopRepairFacilityCode::getPermittedValues())],
            'RFI' => 'required|integer|boolean',
            'MAT' => 'nullable|string|min:1|max:40',
            'SAC' => ['required', 'string', 'min:1', 'max:5', Rule::in(ShopActionCode::getPermittedValues())], // Frederic said make required.
            'SDI' => 'nullable|integer|boolean',
            'PSC' => ['required', 'string', 'min:1', 'max:16', Rule::in(PartStatusCode::getPermittedValues())], // Mal said make required.
            'REM' => 'string|nullable|min:1|max:1000'
        ];
        
    }
    
    protected function SAS_Segment_getFormAttributes()
    {
        return [
        	'SHL' => 'Shop Repair Facility Code',
        	'RFI' => 'Repair Final Action Indicator',
        	'MAT' => 'Manufacturer Authority Text',
        	'SAC' => 'Shop Action Code',
        	'SDI' => 'Shop Disclosure Indicator',
        	'PSC' => 'Part Status Code',
        	'REM' => 'Remarks Text',
        ];
    }
    
    /**
     * Export the SAS_Segment data.
     * Overwrite the INT data with UTAS data.
     *
     * @return array
     */
    protected function SAS_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        $shopFinding = ShopFinding::with('ShopFindingsDetail.Misc_Segment')->find($this->notificationId);
        
        $Misc_Segment = $shopFinding->ShopFindingsDetail->Misc_Segment ?? NULL;
        
        if ($Misc_Segment) {
            $valuesJson = $Misc_Segment->values;
            $valuesObj = json_decode($valuesJson);
            
            // If there's a value pad it either side with a space, except comments which just has a space at the start.
            $SubassemblyName = !empty($valuesObj->SubassemblyName) ? ' ' . $valuesObj->SubassemblyName . ' ' : ' ';
            $Component = !empty($valuesObj->Component) ? ' ' . $valuesObj->Component . ' ' : '';
            $FeatureName = !empty($valuesObj->FeatureName) ? ' ' . $valuesObj->FeatureName . ' ' : ' ';
            $FailureDescription = !empty($valuesObj->FailureDescription) ? ' ' . $valuesObj->FailureDescription . ' ' : ' ';
            $Modifier = !empty($valuesObj->Modifier) ? ' ' . $valuesObj->Modifier . ' ' : ' ';
            $Comments = !empty($valuesObj->Comments) ? ' ' . $valuesObj->Comments : '';
            
            $intData = '[SubassemblyName] =' . $SubassemblyName;
            $intData .= '[Component] =' . $Component;
            $intData .= '[FeatureName] =' . $FeatureName;
            $intData .= '[FailureDescription] =' . $FailureDescription;
            $intData .= '[Modifier] =' . $Modifier;
            $intData .= '[Comments] =' . $Comments;
            
            /*
            This field should be formatted in the following format:
            [SubassemblyName]=xx40xx // ALL CAPS. NO Blank Lines. Spaces OK. Max Characters = 40
            [Component]=xxx80xx // 1st Character CAP. NO Blank Lines. Spaces OK. Max Characters = 40
            [FeatureName]=xx80xx // no caps. Blank Lines are OK. Spaces OK. Max Characters = 40
            [FailureDescription]=xx80xx // no caps. Blanks filled with a ".". Spaces OK. Max Characters = 40
            [Modifier]=xx40xx
            [Comments]=xx80xx
            
            Examples from Mal:
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = OTHER [Component] = Failure Cause Undetermined [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = OTHER [Component] = Failure Cause Undetermined [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = OTHER [Component] = Failure Cause Undetermined [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = OTHER [Component] = Failure Cause Undetermined [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = ACTUATOR [Component] = Actuator [FeatureName] = e-seal [FailureDescription] = worn [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Modify [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = HOUSING [Component] = Damper spring inner [FeatureName] = [FailureDescription] = damaged [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = HOUSING [Component] = Inner damper spring [FeatureName] = [FailureDescription] = [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = ACTUATOR [Component] = Actuator [FeatureName] = inner piston rod set [FailureDescription] = damage [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Recertification [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Modify [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = HOUSING [Component] = Damper spring inner [FeatureName] = [FailureDescription] = damaged [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Modify [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = NO FAILURES HAD OCCURRED [Component] = Test okay [FeatureName] = [FailureDescription] = . [Modifier] = [Comments] =</INT>
            <INT>[SubassemblyName] = EXTERNALLY CAUSED [Component] = Maintenance and handling (induced) [FeatureName] = [FailureDescription] = external damage [Modifier] = [Comments] =</INT>
            
            */
            
            $array['INT'] = $intData;
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
    
    /**
     |-------------
     | Misc_Segment
     |-------------
     */
     
    protected function Misc_Segment_isPresent()
    {
        return true;
    }
     
    protected function Misc_Segment_getName()
    {
        return self::MISC_SEGMENT_NAME;
    }
     
    protected function Misc_Segment_isMandatory()
    {
        return true;
    }
    
    protected function Misc_Segment_getFormInputs()
    {
        $array = [
        	'Plant' => [
            	'title'         => 'Plant',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                //'options'       => PlantCode::getDropDownValues(), // Retrieved via jQuery script.
                'function'      => 'get_MISC_Plant',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 10,
                'display'       => true,
                'input_classes' => ['filter', 'form-control'],
                'default'       => $this->segment->plant_code
        	],
        	'Reason' => [
            	'title'         => 'Reason',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'options'       => [], // Retrieved via jQuery script.
                'function'      => 'get_MISC_Reason',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 20,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
        	],
        	'Type' => [
            	'title'         => 'Type',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1,
                'function'      => 'get_MISC_Type',
                'display'       => true
        	],
        	'PartNo' => [
            	'title'         => 'PartNo',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_MISC_PartNo',
                'display'       => true
        	],
        	'SubassemblyName' => [
            	'title'         => 'Subassembly Name',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'options'       => [], // Retrieved via jQuery script.
                'function'      => 'get_MISC_SubassemblyName',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 30,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'Component' => [
            	'title'         => 'Component',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'function'      => 'get_MISC_Component',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'options'       => [], // Retrieved via jQuery script.
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FeatureName' => [
            	'title'         => 'Feature Name',
                'required'      => false,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'function'      => 'get_MISC_FeatureName',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 50,
                'options'       => [], // Retrieved via jQuery script.
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'FailureDescription' => [
            	'title'         => 'Failure Description',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'options'       => [], // Retrieved via jQuery script.
                'function'      => 'get_MISC_FailureDescription',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 60,
                'display'       => true,
                'input_classes' => ['filter', 'form-control']
            ],
        	'Modifier' => [
            	'title'         => 'Modifier',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'function'      => 'get_MISC_Modifier',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70
            ],
        	'Comments' => [
            	'title'         => 'Comments',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 40,
                'function'      => 'get_MISC_Comments',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 80
            ]
        ];
        
        uasort($array, [static::class, 'orderFormInputs']);
        
        return $array;
    }
    
    protected function Misc_Segment_getValidationRules($id = NULL)
    {
        return [
            'Plant' => ['required', 'integer', Rule::in(PlantCode::getPermittedValues())],
            'Reason' => ['required', 'max:40', 'valid_reason_for_type'],
            'Type' => 'required|in:U,S',
            'PartNo' => 'required',
            'SubassemblyName' => ['required', 'string', new Uppercase, 'min:1', 'max:40'],
            'Component' => ['required', 'string', new FirstLetterUppercase, 'min:1', 'max:40'],
            'FeatureName' => ['nullable', 'string', new Lowercase, 'min:1', 'max:40'], // needs a required_if...
            'FailureDescription' => ['required', 'string', new Lowercase, 'min:1', 'max:40'],
            'Modifier' => ['nullable', 'string', 'min:1', 'max:40'],
            'Comments' => ['nullable', 'string', 'min:1', 'max:40'],
        ];
    }
    
    protected function Misc_Segment_getFormAttributes()
    {
        return [
        	'Plant' => 'Plant',
        	'Reason' => 'Reason',
        	'Type' => 'Supplier Removal Type Code',
        	'PartNo' => 'Manufacturer Full Length Part No.',
        	'SubassemblyName' => 'Subassembly Name',
        	'Component' => 'Component',
        	'FeatureName' => 'Feature Name',
        	'FailureDescription' => 'Failure Description',
        	'Modifier' => 'Modifier',
        	'Comments' => 'Comments',
        ];
    }
    
    protected function Misc_Segment_getValidationMessages()
    {
        return [
            'Type.required' => 'The Supplier Removal Type Code must be set in Received LRU Segment.',
            'PartNo.required' => 'The Manufacturer Full Length Part Number must be set in Received LRU Segment.',
            'empty_when' => 'The :attribute value must be empty when the Supplier Removal Type Code is set to "M", "P" or "O".',
            'valid_reason_for_type' => 'The :attribute is invalid for the Supplier Removal Type Code.'
        ];
    }
    
    /**
     |-------------
     | NHS_Segment
     |-------------
     */
    
    /**
     * Export the NHS_Segment data.
     * Substitute part numbers and cage code.
     *
     * @return array
     */
    protected function NHS_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();
        
        $array = [];
        
        foreach ($attributes as $k => $v) {
            $methodName = $this->segment->getPrefix().$k;
            
            if (method_exists($this->segment, $methodName)) {
                $array[$k] = $this->segment->$methodName();
            }
        }
        
        // Replace with Utas/Collins Cage Code.
        $array['MFR'] = UtasCode::CAGE_CODE;
        
        // Replace part numbers.
        if (!empty($array['MPN'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['MPN']);
            $array['MPN'] = $utasPartNo ?: $array['MPN'];
        }
        
        if (!empty($array['PNR'])) {
            $utasPartNo = UtasPartNumber::getUtasPartNo($array['PNR']);
            $array['PNR'] = $utasPartNo ?: $array['PNR'];
        }
        
        $array = $this->convertDates($this->segment, $attributes, $array);
        $array = $this->cleanData($array);
        
        return $array;
    }
}