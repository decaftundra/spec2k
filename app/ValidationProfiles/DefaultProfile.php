<?php

namespace App\ValidationProfiles;

use App\AircraftDetail;
use App\CageCode;
use App\Location;
use App\Codes\Airline;
use App\Codes\ChangeCode;
use App\Codes\Code;
use App\Codes\ActionCode;
use App\Codes\EnginePositionCode;
use App\Codes\EngineModelCode;
use App\Codes\EngineTypeCode;
use App\Codes\FaultConfirmsAircraftMessageCode;
use App\Codes\FaultConfirmsAircraftPartBiteMessageCode;
use App\Codes\FaultConfirmsReasonForRemovalCode;
use App\Codes\FaultFoundCode;
use App\Codes\FaultInducedCode;
use App\Codes\FinalIndicatorCode;
use App\Codes\HardwareSoftwareFailureCode;
use App\Codes\LocationCode;
use App\Codes\PartStatusCode;
use App\Codes\PrimaryPiecePartFailureIndicator;
use App\Codes\ReasonForRemovalTypeCode;
use App\Codes\RemovalTypeCode;
use App\Codes\ShopActionCode;
use App\Codes\ShopRepairFacilityCode;
use App\Codes\SupplierRemovalTypeCode;
use App\Codes\TimeCycleReferenceCode;
use App\Codes\RepairFinalActionIndicatorCode;
use App\Notification;
use App\Segment;
use App\ValidationProfile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Interfaces\SegmentInterface;
use Illuminate\Foundation\Http\FormRequest;

class DefaultProfile extends ValidationProfile
{
    /**
     * Set the segment name, segment object and notification ID.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface; $segment
     * @param (string) $notificationId
     * @return void
     */
    public function __construct($segmentName, SegmentInterface $segment, $notificationId)
    {
        parent::__construct($segmentName, $segment, $notificationId);
    }

    /**
     * A test to see if this profile should be used.
     * It could be given a form request, a segment, or a notification.
     *
     * @param (string) $segmentName
     * @param \App\Interfaces\SegmentInterface $segment
     * @param (string) $notificationId
     * @return boolean
     */
    public static function useThisProfile($segmentName, SegmentInterface $segment, $notificationId)
    {
        /**
         * Notes:
         * Possibly needs to have access to all attributes to be able to test if this profile should be used.
         * Or maybe only needs to access a couple of attributes search for by the notification id.
         * Search for in order:- 1) request 2) mysql db 3) cached 4) notification
         */

        return true;
    }

    /**
     |-------------
     | HDR_Segment
     |-------------
     */

    protected function HDR_Segment_isMandatory()
    {
        return true;
    }

    protected function HDR_Segment_getFormInputs()
    {
        $array = [
            'CHG' => [
                'title'         => 'Change Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1,
                'options'       => ChangeCode::getDropDownValues(false),
                'function'      => 'get_HDR_CHG',
                'input_width'   => 'col-sm-12 col-md-3',
                'order'         => 10,
                'display'       => true
            ],
            'ROC' => [
                'title'         => 'Reporting Org. Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 3,
                'max'           => 5,
                'function'      => 'get_HDR_ROC',
                'placeholder'   => '',
                'input_width'   => 'col-sm-6 col-md-3',
                'order'         => 30,
                'display'       => true,
                'description'   => 'Use "ZZZZZ" if unavailable.',
                'admin_only'    => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            /*'RDT' => [
                'title'         => 'Reporting Period Date',
                'required'      => true,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'function'      => 'get_HDR_RDT',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20,
                'display'       => true
            ],
            'RSD' => [
                'title'         => 'Reporting Period End Date',
                'required'      => true,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'function'      => 'get_HDR_RSD',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 30,
                'display'       => true
            ],*/
            'OPR' => [
                'title'         => 'Operator Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'function'      => 'get_HDR_OPR',
                'min'           => 3,
                'max'           => 5,
                'placeholder'   => '',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 50,
                'display'       => true,
                'description'   => 'Use "ZZZZZ" if unavailable or no ICAO code.',
                'input_classes' => ['cust-autocomplete', 'form-control']
            ],
            'RON' => [
                'title'         => 'Reporting Org. Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'function'      => 'get_HDR_RON',
                'min'           => 1,
                'max'           => 55,
                'placeholder'   => '',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 20,
                'display'       => true,
                'description'   => '&nbsp;',
                'admin_only'    => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            'WHO' => [
                'title'         => 'Operator Name', // Changed from 'Company Name' Nov 2019.
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'function'      => 'get_HDR_WHO',
                'min'           => 1,
                'max'           => 55,
                'placeholder'   => '',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true,
                'description'   => 'Use the name of the customer returning the part if operator is unknown.',
                'input_classes' => ['cust-autocomplete', 'form-control']
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function HDR_Segment_getValidationRules($id = NULL)
    {
        return [
            'CHG' => ['required', 'string', 'min:1', 'max:1', Rule::in(ChangeCode::getPermittedValues())],
            'ROC' => ['required', 'min:3', 'max:5', Rule::in(CageCode::getPermittedValues())],
            //'RDT' => 'required|date_format:d/m/Y', // This is technically required but is not generated until report is exported.
            //'RSD' => 'required|date_format:d/m/Y', // This is technically required but is not generated until report is exported.
            'OPR' => ['required','nullable','min:3','max:5'],
            'RON' => 'required_if:ROC,ZZZZZ,zzzzz|required_without:ROC|nullable|min:1|max:55',
            'WHO' => 'required_if:OPR,ZZZZZ,zzzzz|required_without:OPR|nullable|min:1|max:55',
        ];
    }

    /*protected function HDR_Segment_conditionalValidation(Validator $validator)
    {
        // Checks that codes and names conform to our locations table where possible.
        $allInput = $validator->getData();

        $code = !empty($allInput['ROC']) ? $allInput['ROC'] : NULL;
        $name = !empty($allInput['RON']) ? $allInput['RON'] : NULL;

        $locationCodes = Location::get()->pluck('cage_code')->toArray();
        $locationNames = Location::get()->pluck('name')->toArray();

        $getCodeByName = Location::where('name', $name)->get()->pluck('cage_code')->toArray();
        $getNameByCode = Location::where('cage_code', $code)->get()->pluck('name')->toArray();

        // If the cage code ROC is in our locations list the name must be accurate
        $validator->sometimes('RON', Rule::in($getNameByCode), function($input) use ($locationCodes) {
            return in_array($input->ROC, $locationCodes);
        });

        // If the organisation name RON is in our locations list the code must be accurate
        $validator->sometimes('ROC', Rule::in($getCodeByName), function($input) use ($locationNames) {
            return in_array($input->RON, $locationNames);
        });

        return $validator;
    }*/

    protected function HDR_Segment_getFormAttributes()
    {
        return [
            'CHG' => 'Change Code',
            'ROC' => 'Reporting Organisation Code',
            //'RDT' => 'Reporting Period Date',
            //'RSD' => 'Reporting Period End Date',
            'OPR' => 'Operator Code',
            'RON' => 'Reporting Organisation Name',
            'WHO' => 'Company Name',
        ];
    }

    /**
     * Optional custom validation messages.
     *
     * @return array
     */
    protected function HDR_Segment_getValidationMessages()
    {
        return [];
    }

    /**
     |-------------
     | AID_Segment
     |-------------
     */

    protected function AID_Segment_isMandatory()
    {
        return false;
    }

    protected function AID_Segment_getFormInputs()
    {
        $array = [
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'placeholder'   => '',
                'function'      => 'get_AID_MFR',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70,
                'display'       => true,
                'input_classes' => ['autocomplete', 'form-control'],
                'description'   => 'Use "ZZZZZ" if unavailable.'
            ],
            'AMC' => [
                'title'         => 'Aircraft Model Identifier',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => '',
                'function'      => 'get_AID_AMC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            'MFN' => [
                'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'placeholder'   => '',
                'function'      => 'get_AID_MFN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 60,
                'input_classes' => ['autocomplete', 'form-control'],
                'display'       => true
            ],
            'ASE' => [
                'title'         => 'Aircraft Series Identifier',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 3,
                'max'           => 10,
                'placeholder'   => '',
                'function'      => 'get_AID_ASE',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 50,
                'display'       => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            'AIN' => [
                'title'         => 'Aircraft Identification No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 10,
                'placeholder'   => '',
                'function'      => 'get_AID_AIN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 20,
                'display'       => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            'REG' => [
                'title'         => 'Aircraft Fully Qualified Registration No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 10,
                'placeholder'   => '',
                'function'      => 'get_AID_REG',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 10,
                'display'       => true,
                'input_classes' => ['autocomplete', 'form-control']
            ],
            'CTH' => [
                'title'         => 'Aircraft Cumulative Total Flight Hours',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'float',
                'placeholder'   => '',
                'min'           => 0,
                'step'          => 0.01,
                'function'      => 'get_AID_CTH',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 72,
                'display'       => true,
                'description'   => 'Use period for decimal point.'
            ],
            'CTY' => [
                'title'         => 'Aircraft Cumulative Total Cycles',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'placeholder'   => '',
                'function'      => 'get_AID_CTY',
                'input_width'   => 'col-sm-6 col-md-6',
                'display'       => true,
                'order'         => 73
            ],
            'OIN' => [
                'title'         => 'Operator Aircraft Internal Identifier',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 10,
                'placeholder'   => '',
                'function'      => 'get_AID_OIN',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 75,
                'display'       => true
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function AID_Segment_getValidationRules($id = NULL)
    {
        // 09/04/2019 Frederic Blanc requested all fields except Manufacturer Code should not allow 'ZZZZZ' to be entered.

        return [
            'MFR' => ['required', 'string', 'min:5', 'max:5', Rule::in(AircraftDetail::getPermittedValues())],
            'AMC' => 'required|string|max:20|not_in:ZZZZZ,zzzzz',
            'MFN' => 'nullable|string|max:55|not_in:ZZZZZ,zzzzz',
            'ASE' => 'nullable|string|min:3|max:10|not_in:ZZZZZ,zzzzz',
            'AIN' => 'required_without_all:REG,OIN|nullable|string|min:1|max:10|not_in:ZZZZZ,zzzzz', // Mal validation requirement.
            'REG' => 'required_without_all:AIN,OIN|nullable|string|min:1|max:10|not_in:ZZZZZ,zzzzz', // Mal validation requirement.
            'OIN' => 'required_without_all:AIN,REG|nullable|string|min:1|max:10|not_in:ZZZZZ,zzzzz', // Mal validation requirement.
            'CTH' => 'nullable|numeric|float:9,2',
            'CTY' => 'nullable|integer|max:999999999|not_in:ZZZZZ,zzzzz',
        ];
    }

    protected function AID_Segment_getValidationMessages()
    {
        return [
            'float' => 'The :attribute value is invalid.'
        ];
    }

    protected function AID_Segment_getFormAttributes()
    {
        return [
            'MFR'        => 'Airframe Manufacture Code',
            'AMC'        => 'Aircraft Model',
            'MFN'        => 'Airframe Manufacturer',
            'ASE'        => 'Aircraft Series',
            'REG'        => 'Aircraft Reg. No.',
            'AIN'        => 'Aircraft Manufacturer Serial Number',
            'OIN'        => 'Operator Aircraft Internal Identifier',
            'CTH'        => 'Aircraft Cumulative Total Flight Hours',
            'CTY'        => 'Aircraft Cumulative Total Cycles',
        ];
    }

    /**
     |-------------
     | EID_Segment
     |-------------
     */

    protected function EID_Segment_isMandatory()
    {
        return false;
    }

    protected function EID_Segment_getFormInputs()
    {
        $array = [
            'AET' => [
                'title'         => 'Engine Type',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'options'       => EngineTypeCode::getDropDownValues(),
                'function'      => 'get_EID_AET',
                'input_width'   => 'col-sm-6 col-md-6 ljmFLOAT ljmFLOATFixClear',
                'order'         => 10,
                'display'       => true
            ],
            'AETO' => [
                'title'         => 'Engine Type: Other',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => 'Please type OTHER value in here.',
                'function'      => 'get_EID_AETO',
                'input_width'   => 'col-sm-6 col-md-6 AETO_container ljmFLOATFixClear',
                'order'         => 15,
                'display'       => true
            ],
            'EPC' => [
                'title'         => 'Engine Position Identifier',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 25,
                'options'       => EnginePositionCode::getDropDownValues(),
                'function'      => 'get_EID_EPC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true,
                'default'       => 'UNK'
            ],
            'AEM' => [
                'title'         => 'Engine Model',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'options'       => EngineModelCode::getDropDownValues(),
                'function'      => 'get_EID_AEM',
                'input_width'   => 'col-sm-6 col-md-6 ljmFLOAT ljmFLOATFixClear',
                'order'         => 20,
                'display'       => true
            ],
            'AEMO' => [
                'title'         => 'Engine Model: Other',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => 'Please type OTHER value in here.',
                'function'      => 'get_EID_AEMO',
                'input_width'   => 'col-sm-6 col-md-6 AEMO_container ljmFLOATFixClear',
                'order'         => 25,
                'display'       => true
            ],
            'EMS' => [
                'title'         => 'Engine Serial Number',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => '',
                'function'      => 'get_EID_EMS',
                'input_width'   => 'col-sm-6 col-md-6 ljmFLOATFixClear',
                'order'         => 30,
                'display'       => true
            ],
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'placeholder'   => '',
                'function'      => 'get_EID_MFR',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 50
            ],
            'ETH' => [
                'title'         => 'Engine Cumulative Total Flight Hours',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'float',
                'min'           => 0,
                'step'          => 0.01,
                'function'      => 'get_EID_ETH',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 60,
                'description'   => 'Use period for decimal point.'
            ],
            'ETC' => [
                'title'         => 'Engine Cumulative Total Cycles',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'step'          => .01,
                'function'      => 'get_EID_ETC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70
            ],
            'LJMFILTERINFO' => [
                'title'         => '',
                'required'      => false,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'function'      => 'get_EID_LJMFILTERINFO',
                'min'           => 1,
                'max'           => 10000000,
                'order'         => 80
            ],
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function EID_Segment_getValidationRules($id = NULL)
    {
        // Create black list of codes but remove 'ZZZZZ' and 'zzzzz'.
        $cageCodeBlackList = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());

        foreach ($cageCodeBlackList as $key => $val) {
            if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
                unset($cageCodeBlackList[$key]);
            }
        }

        return [
            'AET' => 'required_without:AETO',
            'EPC' => ['required', 'string', 'max:25', Rule::in(EnginePositionCode::getPermittedValues())],
            'AEM' => 'required_without:AEMO',
            'EMS' => 'nullable|string|max:20',
            'MFR' => ['nullable', 'string', 'min:5', 'max:5', Rule::notIn($cageCodeBlackList)],
            'ETH' => 'nullable|numeric|float:9,2',
            'ETC' => 'nullable|integer|max:999999999'
        ];
    }

    protected function EID_Segment_getValidationMessages()
    {
        return [
            'float' => 'The :attribute value is invalid.'
        ];
    }

    protected function EID_Segment_getFormAttributes()
    {
        return [
            'AET' => 'Engine Type',
            'AETO' => 'Engine Type: Other',
            'EPC' => 'Engine Position Identifier',
            'AEM' => 'Engine Model',
            'AEMO' => 'Engine Model: Other',
            'EMS' => 'Engine Serial Number',
            'MFR' => 'Manufacturer Code',
            'ETH' => 'Engine Cumulative Total Flight Hours',
            'ETC' => 'Engine Cumulative Total Cycles',
        ];
    }

    /**
     |-------------
     | API_Segment
     |-------------
     */

    protected function API_Segment_isMandatory()
    {
        return false;
    }

    protected function API_Segment_getFormInputs()
    {
        $array = [
            'AET' => [
                'title'         => 'APU Type',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => '',
                'function'      => 'get_API_AET',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'display'       => true
            ],
            'EMS' => [
                'title'         => 'APU Serial Number',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'placeholder'   => '',
                'function'      => 'get_API_EMS',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 30,
                'display'       => true
            ],
            'AEM' => [
                'title'         => 'APU Model',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'placeholder'   => '',
                'function'      => 'get_API_AEM',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20
            ],
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'placeholder'   => '',
                'function'      => 'get_API_MFR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 40
            ],
            'ATH' => [
                'title'         => 'APU Cumulative Total Hours',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'float',
                'step'          => 0.01,
                'min'           => 0,
                'function'      => 'get_API_ATH',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50,
                'description'   => 'Use period for decimal point.'
            ],
            'ATC' => [
                'title'         => 'APU Cumulative Total Cycles',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_API_ATC',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 60
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function API_Segment_getValidationRules($id = NULL)
    {
        // Create black list of codes but remove 'ZZZZZ' and 'zzzzz'.
        $cageCodeBlackList = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());

        foreach ($cageCodeBlackList as $key => $val) {
            if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
                unset($cageCodeBlackList[$key]);
            }
        }

        return [
            'AET' => 'required|string|max:20',
            'EMS' => 'required|string|max:20',
            'AEM' => 'nullable|string|max:32',
            'MFR' => ['nullable', 'string', 'min:5', 'max:5', Rule::notIn($cageCodeBlackList)],
            'ATH' => 'nullable|numeric|float:9,2',
            'ATC' => 'nullable|integer|max:999999999',
        ];
    }

    protected function API_Segment_getFormAttributes()
    {
        return [
            'AET' => 'APU Type',
            'EMS' => 'APU Serial Number',
            'AEM' => 'APU Model',
            'MFR' => 'Manufacturer Code',
            'ATH' => 'APU Cumulative Total Hours',
            'ATC' => 'APU Cumulative Total Cycles',
        ];
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
                'input_width'   => 'col-sm-6 col-md-6',
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
                'display'       => true,
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
                'input_width'   => 'col-sm-12 col-md-4',
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
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 139
            ],
            'REM' => [
                'title'         => 'Remarks Text',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_RCS_REM',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 136,
                'display'       => true
            ]
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
            'INT' => 'nullable|string|min:1|max:5000',
            'REM' => 'nullable|string|min:1|max:1000',
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
            'REM' => 'Remark Text',
            'MFN' => 'Manufacturer Name'
        ];
    }

    protected function RCS_Segment_conditionalValidation(Validator $validator)
    {
        $validator->sometimes('FBC', 'in:NA', function($input){
            if (in_array($input->FCR, ['NC', 'NA'])) {
                return true;
            } elseif ($input->FHS == 'NA') {
                return true;
            } elseif (in_array($input->FFI, ['IN', 'NA'])) {
                return true;
            } elseif (in_array($input->FFC, ['NT', 'NA'])) {
                return true;
            }

            return false;
        });

        $validator->sometimes('FAC', 'in:NA', function($input){
            if (in_array($input->FCR, ['NC', 'NA'])) {
                return true;
            } elseif ($input->FHS == 'NA') {
                return true;
            } elseif (in_array($input->FFI, ['IN', 'NA'])) {
                return true;
            } elseif (in_array($input->FFC, ['NT', 'NA'])) {
                return true;
            }

            return false;
        });

        $validator->sometimes('FCR', 'in:NA', function($input){
            if (in_array($input->RRC, ['M', 'S'])) {
                return true;
            } elseif ($input->FHS == 'NA') {
                return true;
            } elseif (in_array($input->FFI, ['IN', 'NA'])) {
                return true;
            } elseif (in_array($input->FFC, ['NT', 'NA'])) {
                return true;
            }

            return false;
        });

        $validator->sometimes('FCR', 'in:CR,NC', function($input){
            return (!in_array($input->RRC, ['M', 'S'])) && (in_array($input->FHS, ['HW', 'SW']));
        });

        $validator->sometimes('FHS', 'in:NA', function($input){
            if (in_array($input->FFI, ['IN', 'NA'])) {
                return true;
            } elseif (in_array($input->FFC, ['NT', 'NA'])) {
                return true;
            }

            return false;
        });

        // Hardware/Software Failure Code must be either HW or SW if Failure/Fault Induced Code is NI.
        $validator->sometimes('FHS', 'in:HW,SW', function($input){
            return $input->FFI == 'NI';
        });

        // Failure/Fault Induced Code must be NA if Failure/Fault Found Code is NA or NT.
        $validator->sometimes('FFI', 'in:NA', function($input){
            return in_array($input->FFC, ['NT', 'NA']);
        });

        // Failure/Fault Induced Code must be either NI or IN if Failure/Fault Found Code is FT.
        $validator->sometimes('FFI', 'in:NI,IN', function($input){
            return $input->FFC == 'FT';
        });

        // Failure/Fault Found Code must be NA if Supplier Removal Type Code is M and the Fault Found Code is not FT.
        $validator->sometimes('FFC', 'in:NA', function($input){
            return ($input->RRC == 'M') && ($input->FFC != 'FT');
        });

        return $validator;
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
            'INT' => [
                'title'         => 'Inspection/Shop Action Text',
                'required'      => true,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 5000,
                'function'      => 'get_SAS_INT',
                'input_width'    => 'col-sm-12 col-md-12',
                'order'         => 10,
                'display'       => true
            ],
            'SHL' => [
                'title'         => 'Shop Repair Facility Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 3,
                'options'       => ShopRepairFacilityCode::getDropDownValues(),
                'function'      => 'get_SAS_SHL',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true,
                'default'       => 'R2'
            ],
            'RFI' => [
                'title'         => 'Repair Final Action Indicator',
                'required'      => true,
                'input_type'    => 'radio',
                'data_type'     => 'boolean',
                'function'      => 'get_SAS_RFI',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 60,
                'options'       => RepairFinalActionIndicatorCode::getDropDownValues(false),
                'description'   => 'Shop returning the part certified back to service.',
                'display'       => true
            ],
            'MAT' => [
                'title'         => 'Manufacturer Authority Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
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
                'input_width'    => 'col-sm-12 col-md-12',
                'order'         => 80
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function SAS_Segment_getValidationRules($id = NULL)
    {
        return [
            'INT' => 'required|string|min:1|max:5000',
            'SHL' => ['required', 'string', 'min:1', 'max:3', Rule::in(ShopRepairFacilityCode::getPermittedValues())],
            'RFI' => 'required|integer|boolean',
            'MAT' => 'nullable|string|min:1|max:40',
            'SAC' => ['required', 'string', 'min:1', 'max:5', Rule::in(ShopActionCode::getPermittedValues())], // Frederic said make Required.
            'SDI' => 'nullable|integer|boolean',
            'PSC' => ['required', 'string', 'min:1', 'max:16', Rule::in(PartStatusCode::getPermittedValues())], // Mal said make required.
            'REM' => 'string|nullable|min:1|max:1000'
        ];
    }

    protected function SAS_Segment_conditionalValidation(Validator $validator) {
        $validator->sometimes('SAC', Rule::in(ActionCode::getActionCodes(1, $SAC = NULL)->pluck('SAC')->toArray()), function($input){
            return $input->RFI == 1;
        });

        $validator->sometimes('SAC', Rule::in(ActionCode::getActionCodes(0, $SAC = NULL)->pluck('SAC')->toArray()), function($input){
            return $input->RFI == 0;
        });

        return $validator;
    }

    protected function SAS_Segment_getFormAttributes()
    {
        return [
            'INT' => 'Inspection/Shop Action Text',
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
     |-------------
     | SUS_Segment
     |-------------
     */

    protected function SUS_Segment_isMandatory()
    {
        return false;
    }

    protected function SUS_Segment_getFormInputs()
    {
        $array = [
            'SHD' => [
                'title'         => 'Shipped Date',
                'required'      => true,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'placeholder'   => 'dd/mm/yyyy',
                'function'      => 'get_SUS_SHD',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'display'       => true
            ],
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_SUS_MFR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20,
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
                'function'      => 'get_SUS_MPN',
                'input_width'   => 'col-sm-6 col-md-4',
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
                'max'           => 30,
                'function'      => 'get_SUS_SER',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50,
                'description'   => 'Use "ZZZZZ" if unavailable.',
                'display'       => true,
                'admin_only'    => true
            ],
            'MFN' => [
                'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'placeholder'   => '',
                'function'      => 'get_SUS_MFN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 30,
                'display'       => true,
                'description'   => '&nbsp;' // For layout purposes only.
            ],
            'PDT' => [
                'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_SUS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 70
            ],
            'PNR' => [
                'title'         => 'Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_SUS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 60,
                'description'   => 'Use "ZZZZZ" if unavailable and submit Airline Stock No.',
                'display'       => true
            ],
            'OPN' => [
                'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_SUS_OPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 80
            ],
            'USN' => [
                'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 35,
                'function'      => 'get_SUS_USN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 90
            ],
            'ASN' => [
                'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_SUS_ASN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 100,
                'display'       => true
            ],
            'UCN' => [
                'title'         => 'Unique Component Identification No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_SUS_UCN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 110,
                'display'       => true
            ],
            'SPL' => [
                'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_SUS_SPL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 120
            ],
            'UST' => [
                'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_SUS_UST',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 130
            ],
            'PML' => [
                'title'         => 'Part Modification Level',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_SUS_PML',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 150
            ],
            'PSC' => [
                'title'         => 'Part Status Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 16,
                'function'      => 'get_SUS_PSC',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 140
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function SUS_Segment_getValidationRules($id = NULL)
    {
        return [
            'SHD' => 'required|date_format:d/m/Y',
            'MFR' => ['required', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
            'MPN' => 'required|string|min:1|max:32',
            'SER' => 'required|string|min:1|max:30',
            'MFN' => 'required_if:MFR,ZZZZZ,zzzzz|nullable|string|min:1|max:55',
            'PDT' => 'nullable|string|min:1|max:100',
            'PNR' => 'nullable|string|min:1|max:32',
            'OPN' => 'nullable|string|min:16|max:32',
            'USN' => 'nullable|string|min:6|max:35',
            'ASN' => 'required_if:PNR,ZZZZZ,zzzzz|nullable|string|min:1|max:32',
            'UCN' => 'required_if:SER,ZZZZZ,zzzzz|nullable|string|min:1|max:15',
            'SPL' => 'nullable|string|min:5|max:5',
            'UST' => 'nullable|string|min:6|max:20',
            'PML' => 'nullable|string|min:1|max:1000',
            'PSC' => 'nullable|string|min:1|max:16',
        ];
    }

    protected function SUS_Segment_getFormAttributes()
    {
        return [
            'SHD' => 'Shipped Date',
            'MFR' => 'Manufacturer Code',
            'MPN' => 'Manufacturer Full Length Part Number',
            'SER' => 'Part Serial Number',
            'MFN' => 'Manufacturer Name',
            'PDT' => 'Part Description',
            'PNR' => 'Part Number',
            'OPN' => 'Overlength Part Number',
            'USN' => 'Universal Serial Number',
            'ASN' => 'Airline Stock Number',
            'UCN' => 'Unique Component Identification Number',
            'SPL' => 'Supplier Code',
            'UST' => 'Universal Serial Tracking Number',
            'PML' => 'Part Modification Level',
            'PSC' => 'Part Status Code',
        ];
    }

    /**
     |-------------
     | RLS_Segment
     |-------------
     */

    protected function RLS_Segment_isMandatory()
    {
        return false;
    }

    /**
     * Return an array of form input information.
     *
     * @return array $array
     */
    protected function RLS_Segment_getFormInputs()
    {
        $array = [
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RLS_MFR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'description'   => 'Use "ZZZZZ" if unavailable and submit Manufacturer Name.',
                'display'       => true
            ],
            'MPN' => [
                'title'         => 'Manufacturer Full Length Part No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RLS_MPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20,
                'display'       => true,
                'admin_only'    => true
            ],
            'SER' => [
                'title'         => 'Part Serial No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 30,
                'function'      => 'get_RLS_SER',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 30,
                'description'   => 'Use "ZZZZZ" if unavailable and submit Component Id No.',
                'display'       => true,
                'admin_only'    => true
            ],
            'RED' => [
                'title' => 'Part Removal Date',
                'required' => true,
                'input_type' => 'date',
                'data_type' => 'string',
                'function' => 'get_RLS_RED',
                'input_width' => 'col-sm-6 col-md-6',
                'order' => 40,
                'display' => true
            ],
            'MFN' => [
                'title' => 'Manufacturer Name',
                'required' => false,
                'input_type' => 'text',
                'data_type' => 'string',
                'min' => 1,
                'max' => 55,
                'placeholder' => '',
                'function' => 'get_RLS_MFN',
                'input_width' => 'col-sm-6 col-md-4',
                'order' => 45,
                'display' => true  /* LJMJun23 MGTSUP-518 */
            ],
            'TTY' => [
                'title'         => 'Removal Type Code',
                'required'      => false,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1,
                'placeholder'   => '',
                'options'       => RemovalTypeCode::getDropDownValues(),
                'function'      => 'get_RLS_TTY',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50
            ],
            'RFR' => [
                'title' => 'Reason for Removal Code',
                'required' => false,
                'input_type' => 'select',
                'data_type' => 'string',
                'options' => ReasonForRemovalTypeCode::getDropDownValues(),
                'function' => 'get_RLS_RFR',
                'input_width' => 'col-sm-6 col-md-4',
                'order' => 55
            ],
            'RET' => [
                'title'         => 'Reason for Removal Clarification Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 64,
                'options'       => [],
                'function'      => 'get_RLS_RET',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 60
            ],
            'DOI' => [
                'title'         => 'Installation Date',
                'required'      => false,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'placeholder'   => 'dd/mm/yyyy',
                'function'      => 'get_RLS_DOI',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 70
            ],
            'PNR' => [
                'title'         => 'Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RLS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 90
            ],
            'OPN' => [
                'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_RLS_OPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 100
            ],
            'USN' => [
                'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 35,
                'function'      => 'get_RLS_USN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 110
            ],
            'RMT' => [
                'title'         => 'Removal Reason Text',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 5000,
                'function'      => 'get_RLS_RMT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 120
            ],
            'APT' => [
                'title'         => 'Aircraft Engine/APU Position Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_RLS_APT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 130
            ],
            'CPI' => [
                'title'         => 'Component Position Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 25,
                'placeholder'   => '',
                'function'      => 'get_RLS_CPI',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 140
            ],
            'CPT' => [
                'title'         => 'Component Position Text',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'placeholder'   => '',
                'function'      => 'get_RLS_CPT',
                'input_width'   => 'col-sm-6 col-md-8',
                'order'         => 150
            ],
            'PDT' => [
                'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_RLS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 160
            ],
            'PML' => [
                'title'         => 'Part Modification Level',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_RLS_PML',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 170
            ],
            'ASN' => [
                'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RLS_ASN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 180
            ],
            'UCN' => [
                'title'         => 'Unique Component Identification No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RLS_UCN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 190
            ],
            'SPL' => [
                'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RLS_SPL',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 200
            ],
            'UST' => [
                'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 20,
                'function'      => 'get_RLS_UST',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 210
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    /**
     * Get validation rules.
     *
     * @param (int) $id
     * @return array
     */
    protected function RLS_Segment_getValidationRules($id = NULL)
    {
        return [
            'MFR' => ['required', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
            'MPN' => 'required|string|min:1|max:32',
            'SER' => 'required|string|min:1|max:30',
            'RED' => 'required|date_format:d/m/Y',
            'TTY' => ['nullable', 'string', Rule::in(RemovalTypeCode::getPermittedValues())],
            'RET' => 'required_if:TTY,O|required_if:RFR,ZZ|nullable|string|min:1|max:64',
            'DOI' => 'nullable|date_format:d/m/Y',
            'MFN' => 'required_if:MFR,ZZZZZ,zzzzz|nullable|string|min:1|max:55',
            'PNR' => 'nullable|string|min:1|max:32',
            'OPN' => 'nullable|string|min:16|max:32',
            'USN' => 'nullable|string|min:6|max:35',
            'RMT' => 'nullable|string|min:1|max:5000',
            'APT' => 'nullable|string|min:1|max:100',
            'CPI' => 'nullable|string|min:1|max:25',
            'CPT' => 'nullable|string|min:1|max:100',
            'PDT' => 'nullable|string|min:1|max:100',
            'PML' => 'nullable|string|min:1|max:1000',
            'ASN' => 'nullable|string|min:1|max:32',
            'UCN' => 'required_if:SER,ZZZZZ,zzzzz|nullable|string|min:1|max:15',
            'SPL' => 'nullable|string|min:5|max:5',
            'UST' => 'nullable|string|min:6|max:20',
            'RFR' => ['nullable', 'string', Rule::in(ReasonForRemovalTypeCode::getPermittedValues())]
        ];
    }

    protected function RLS_Segment_getFormAttributes()
    {
        return [
            'MFR' => 'Manufacturer Code',
            'MPN' => 'Manufacturer Full Length Part Number',
            'SER' => 'Part Serial Number',
            'RED' => 'Part Removal Date',
            'TTY' => 'Removal Type Code',
            'RET' => 'Reason for Removal Clarification Text',
            'DOI' => 'Installation Date',
            'MFN' => 'Manufacturer Name',
            'PNR' => 'Part Number',
            'OPN' => 'Overlength Part Number',
            'USN' => 'Universal Serial Number',
            'RMT' => 'Removal Reason Text',
            'APT' => 'Aircraft Engine/APU Position Text',
            'CPI' => 'Component Position Code',
            'CPT' => 'Component Position Text',
            'PDT' => 'Part Description',
            'PML' => 'Part Modification Level',
            'ASN' => 'Airline Stock Number',
            'UCN' => 'Unique Component Identification Number',
            'SPL' => 'Supplier Code',
            'UST' => 'Universal Serial Tracking Number',
            'RFR' => 'Reason for Removal Code',
        ];
    }

    /**
     |-------------
     | LNK_Segment
     |-------------
     */

    protected function LNK_Segment_isMandatory()
    {
        return false;
    }

    protected function LNK_Segment_getFormInputs()
    {
        return [
            'RTI' => [
                'title'         => 'Removal Tracking Identifier',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 50,
                'function'      => 'get_LNK_RTI',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 10,
                'display'       => true
            ]
        ];
    }

    protected function LNK_Segment_getValidationRules($id = NULL)
    {
        return [
            'RTI' => 'required|string|min:1|max:50'
        ];
    }

    protected function LNK_Segment_getFormAttributes()
    {
        return [
            'RTI' => 'Removal Tracking Identifier',
        ];
    }

    /**
     |-------------
     | ATT_Segment
     |-------------
     */

    protected function ATT_Segment_isMandatory()
    {
        return false;
    }

    protected function ATT_Segment_getFormInputs()
    {
        $array = [
            'TRF' => [
                'title'         => 'Time/Cycle Reference Code',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1,
                'options'       => TimeCycleReferenceCode::getDropDownValues(),
                'function'      => 'get_ATT_TRF',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 10,
                'display'       => true
            ],
            'OTT' => [
                'title'         => 'Operating Time',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_ATT_OTT',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 20,
                'display'       => true
            ],
            'OPC' => [
                'title'         => 'Operating Cycle Count',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_ATT_OPC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 30,
                'display'       => true
            ],
            'ODT' => [
                'title'         => 'Operating Days',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_ATT_ODT',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true
            ],
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function ATT_Segment_getValidationRules($id = NULL)
    {
        return [
            'TRF' => ['required', 'string', 'min:1', 'max:1', Rule::in(TimeCycleReferenceCode::getPermittedValues())],
            'OTT' => 'required_without_all:OPC,ODT|nullable|integer|max:999999',
            'OPC' => 'required_without_all:OTT,ODT|nullable|integer|max:999999',
            'ODT' => 'required_without_all:OTT,OPC|nullable|integer|max:999999',
        ];
    }

    protected function ATT_Segment_getFormAttributes()
    {
        return [
            'TRF' => 'Time/Cycle Reference Code',
            'OTT' => 'Operating Time',
            'OPC' => 'Operating Cycle Count',
            'ODT' => 'Operating Days',
        ];
    }

    /**
     |-------------
     | SPT_Segment
     |-------------
     */

    protected function SPT_Segment_isMandatory()
    {
        return false;
    }

    protected function SPT_Segment_getFormInputs()
    {
        $array = [
            'MAH' => [
                'title'         => 'Total Labor Hours',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'float',
                'min'           => 0,
                'placeholder'   => '',
                'step'          => 0.01,
                'function'      => 'get_SPT_MAH',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'display'       => true
            ],
            'FLW' => [
                'title'         => 'Shop Flow Time',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_SPT_FLW',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20,
                'display'       => true
            ],
            'MST' => [
                'title'         => 'Mean Shop Processing Time',
                'required'      => false,
                'input_type'    => 'number',
                'data_type'     => 'integer',
                'min'           => 1,
                'function'      => 'get_SPT_MST',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 30,
                'display'       => true
            ],
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function SPT_Segment_getValidationRules($id = NULL)
    {
        return [
            'MAH' => 'required_without_all:FLW,MST|nullable|numeric|float:8,2',
            'FLW' => 'required_without_all:MAH,MST|nullable|integer|min:1|max:999999999',
            'MST' => 'required_without_all:MAH,FLW|nullable|integer|min:1|max:9999',
        ];
    }

    protected function SPT_Segment_getValidationMessages()
    {
        return [
            'float' => 'The :attribute value is invalid.'
        ];
    }

    protected function SPT_Segment_getFormAttributes()
    {
        return [
            'MAH' => 'Total Labor Hours',
            'FLW' => 'Shop Flow Time',
            'MST' => 'Mean Shop Processing Time'
        ];
    }

    /**
     |-------------
     | WPS_Segment
     |-------------
     */

    protected function WPS_Segment_isMandatory()
    {
        return true;
    }

    protected function WPS_Segment_getFormInputs()
    {
        $array = [
            'SFI' => [
                'title'         => 'Shop Findings Record Identifier',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 50,
                'function'      => 'get_WPS_SFI',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'display'       => true
            ],
            'PPI' => [
                'title'         => 'Piece Part Record Identifier',
                'required'      => true,
                'input_type'    => 'hidden',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 50,
                'function'      => 'get_WPS_PPI',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20,
                'display'       => true
            ],
            'PFC' => [
                'title'         => 'Primary Piece Part Failure Indicator',
                'required'      => true,
                'input_type'    => 'select',
                'data_type'     => 'string',
                'options'       => PrimaryPiecePartFailureIndicator::getDropDownValues(),
                'function'      => 'get_WPS_PFC',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 30,
                'display'       => true,
                'default'       => 'D'
            ],
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_WPS_MFR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50
            ],
            'MFN' => [
                'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'function'      => 'get_WPS_MFN',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 70
            ],
            'MPN' => [
                'title'         => 'Manufacturer Full Length Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_WPS_MPN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 30,
                'display'       => true,
                'admin_only'    => true
            ],
            'SER' => [
                'title'         => 'Part Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_WPS_SER',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 60
            ],
            'FDE' => [
                'title'         => 'Piece Part Failure Description',
                'required'      => false,
                'input_type'    => 'textarea',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 1000,
                'function'      => 'get_WPS_FDE',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 80,
                'display'       => true
            ],
            'PNR' => [
                'title'         => 'Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_WPS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 90
            ],
            'OPN' => [
                'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_WPS_OPN',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 110
            ],
            'USN' => [
                'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_WPS_USN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 100
            ],
            'PDT' => [
                'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_WPS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 120,
                'display'       => true,
                'admin_only'    => true
            ],
            'GEL' => [
                'title'         => 'Geographic/Electrical Location',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 30,
                'function'      => 'get_WPS_GEL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 130
            ],
            'MRD' => [
                'title'         => 'Material Receipt Date',
                'required'      => false,
                'input_type'    => 'date',
                'data_type'     => 'string',
                'function'      => 'get_WPS_MRD',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 140
            ],
            'ASN' => [
                'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_WPS_ASN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 150
            ],
            'UCN' => [
                'title'         => 'Unique Component ID No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_WPS_UCN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 160
            ],
            'SPL' => [
                'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_WPS_SPL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 170
            ],
            'UST' => [
                'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_WPS_UST',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 180
            ],
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function WPS_Segment_getValidationRules($id = NULL)
    {
        return [
            'SFI' => 'required|string|min:1|max:50',
            'PPI' => ['required', 'string', 'min:1', 'max:50', Rule::unique('WPS_Segments')->ignore($id, 'PPI')],
            'PFC' => ['required', 'string', 'min:1', 'max:1', Rule::in(PrimaryPiecePartFailureIndicator::getPermittedValues())],
            'MFR' => ['nullable', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
            'MFN' => 'nullable|string|min:1|max:55',
            'MPN' => 'required_without:PDT|nullable|string|min:1|max:32',
            'SER' => 'nullable|string|min:1|max:15',
            'FDE' => 'nullable|string|min:1|max:1000',
            'PNR' => 'nullable|string|min:1|max:15',
            'OPN' => 'nullable|string|min:16|max:32',
            'USN' => 'nullable|string|min:6|max:20',
            'PDT' => 'required_without:MPN|nullable|string|min:1|max:100',
            'GEL' => 'nullable|string|min:1|max:30',
            'MRD' => 'nullable|date_format:d/m/Y',
            'ASN' => 'nullable|string|min:1|max:32',
            'UCN' => 'nullable|string|min:1|max:15',
            'SPL' => 'nullable|string|min:5|max:5',
            'UST' => 'nullable|string|min:6|max:20',
        ];
    }

    protected function WPS_Segment_getFormAttributes()
    {
        return [
            'SFI' => 'Shop Findings Record Identifier',
            'PPI' => 'Piece Part Record Identifier',
            'PFC' => 'Primary Piece Part Failure Indicator',
            'MFR' => 'Manufacturer Code',
            'MFN' => 'Manufacturer Name',
            'MPN' => 'Manufacturer Full Length Part No.',
            'SER' => 'Part Serial No.',
            'FDE' => 'Piece Part Failure Description',
            'PNR' => 'Part No.',
            'OPN' => 'Overlength Part No.',
            'USN' => 'Universal Serial No.',
            'PDT' => 'Part Description',
            'GEL' => 'Geographic/Electrical Location',
            'MRD' => 'Material Receipt Date',
            'ASN' => 'Airline Stock No.',
            'UCN' => 'Unique Component ID No.',
            'SPL' => 'Supplier Code',
            'UST' => 'Universal Serial Tracking No.'
        ];
    }

    /**
     |-------------
     | NHS_Segment
     |-------------
     */

    protected function NHS_Segment_isMandatory()
    {
        return false;
    }

    protected function NHS_Segment_getFormInputs()
    {
        $array = [
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_NHS_MFR',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 10,
                'description'   => 'Use "ZZZZZ" if CAGE/NCAGE unavailable.',
                'display'       => true
            ],
            'MPN' => [
                'title'         => 'Manufacturer Full Length Part No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_NHS_MPN',
                'input_width'   => 'col-sm-12 col-md-6',
                'order'         => 30,
                'display'       => true
            ],
            'SER' => [
                'title'         => 'Part Serial No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_NHS_SER',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 20,
                'description'   => 'Use "ZZZZZ" if unavailable.',
                'display'       => true
            ],
            'MFN' => [
                'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'function'      => 'get_NHS_MFN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 40,
                'display'       => true

            ],
            'PNR' => [
                'title'         => 'Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_NHS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50
            ],
            'OPN' => [
                'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_NHS_OPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 60
            ],
            'USN' => [
                'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_NHS_USN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 70
            ],
            'PDT' => [
                'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_NHS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 80
            ],
            'ASN' => [
                'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_NHS_ASN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 90
            ],
            'UCN' => [
                'title'         => 'Unique Component Identification No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_NHS_UCN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 100
            ],
            'SPL' => [
                'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_NHS_SPL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 110
            ],
            'UST' => [
                'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_NHS_UST',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 120
            ],
            'NPN' => [
                'title'         => 'Failed Piece Part Next Higher Assembly NHA Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_NHS_NPN',
                'input_width'   => 'col-sm-12 col-md-8',
                'order'         => 130
            ],
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function NHS_Segment_getValidationRules($id = NULL)
    {
        return [
            'MFR' => ['required', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
            'MPN' => 'required|string|min:1|max:32',
            'SER' => 'required|string|min:1|max:15',
            'MFN' => 'required_if:MFR,ZZZZZ,zzzzz|nullable|string|min:1|max:55',
            'PNR' => 'nullable|string|min:1|max:15',
            'OPN' => 'nullable|string|min:16|max:32',
            'USN' => 'nullable|string|min:6|max:20',
            'PDT' => 'nullable|string|min:1|max:100',
            'ASN' => 'nullable|string|min:1|max:32',
            'UCN' => 'nullable|string|min:1|max:15',
            'SPL' => 'nullable|string|min:5|max:5',
            'UST' => 'nullable|string|min:6|max:20',
            'NPN' => 'nullable|string|min:1|max:32',
        ];
    }

    protected function NHS_Segment_getFormAttributes()
    {
        return [
            'MFR' => 'Manufacturer Code',
            'MPN' => 'Manufacturer Full Length Part No.',
            'SER' => 'Part Serial No.',
            'MFN' => 'Manufacturer Name',
            'PNR' => 'Part No.',
            'OPN' => 'Overlength Part No.',
            'USN' => 'Universal Serial No.',
            'PDT' => 'Part Description',
            'ASN' => 'Airline Stock No.',
            'UCN' => 'Unique Component Identification No.',
            'SPL' => 'Supplier Code',
            'UST' => 'Universal Serial Tracking No.',
            'NPN' => 'Failed Piece Part Next Higher Assembly NHA Part No.'
        ];
    }

    /**
     |-------------
     | RPS_Segment
     |-------------
     */

    protected function RPS_Segment_isMandatory()
    {
        return false;
    }

    protected function RPS_Segment_getFormInputs()
    {
        $array = [
            'MPN' => [
                'title'         => 'Manufacturer Full Length Part No.',
                'required'      => true,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RPS_MPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 10,
                'display'       => true,
                'admin_only'    => true
            ],
            'MFR' => [
                'title'         => 'Manufacturer Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RPS_MFR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 20
            ],
            'MFN' => [
                'title'         => 'Manufacturer Name',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 55,
                'function'      => 'get_RPS_MFN',
                'input_width'   => 'col-sm-12 col-md-4',
                'order'         => 30
            ],
            'SER' => [
                'title'         => 'Part Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RPS_SER',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 65,
                'description'   => 'Use ZZZZZ if unavailable.'
            ],
            'PNR' => [
                'title'         => 'Part Number',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RPS_PNR',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 50
            ],
            'OPN' => [
                'title'         => 'Overlength Part No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 16,
                'max'           => 32,
                'function'      => 'get_RPS_OPN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 60
            ],
            'USN' => [
                'title'         => 'Universal Serial No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_RPS_USN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 70
            ],
            'ASN' => [
                'title'         => 'Airline Stock No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 32,
                'function'      => 'get_RPS_ASN',
                'input_width'   => 'col-sm-6 col-md-6',
                'order'         => 80
            ],
            'UCN' => [
                'title'         => 'Unique Component Id No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 15,
                'function'      => 'get_RPS_UCN',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 90
            ],
            'SPL' => [
                'title'         => 'Supplier Code',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 5,
                'max'           => 5,
                'function'      => 'get_RPS_SPL',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 100
            ],
            'UST' => [
                'title'         => 'Universal Serial Tracking No.',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 6,
                'max'           => 20,
                'function'      => 'get_RPS_UST',
                'input_width'   => 'col-sm-6 col-md-4',
                'order'         => 110
            ],
            'PDT' => [
                'title'         => 'Part Description',
                'required'      => false,
                'input_type'    => 'text',
                'data_type'     => 'string',
                'min'           => 1,
                'max'           => 100,
                'function'      => 'get_RPS_PDT',
                'input_width'   => 'col-sm-12 col-md-12',
                'order'         => 120
            ]
        ];

        uasort($array, [static::class, 'orderFormInputs']);

        return $array;
    }

    protected function RPS_Segment_getValidationRules($id = NULL)
    {
        return [
            'MPN' => 'required|string|min:1|max:32',
            'MFR' => ['nullable', 'string', 'min:5', 'max:5', Rule::in(CageCode::getPermittedValues())],
            'MFN' => 'nullable|string|min:1|max:55',
            'SER' => 'nullable|string|min:1|max:15',
            'PNR' => 'nullable|string|min:1|max:15',
            'OPN' => 'nullable|string|min:16|max:32',
            'USN' => 'nullable|string|min:6|max:20',
            'ASN' => 'nullable|string|min:1|max:32',
            'UCN' => 'nullable|string|min:1|max:15',
            'SPL' => 'nullable|string|min:5|max:5',
            'UST' => 'nullable|string|min:6|max:20',
            'PDT' => 'nullable|string|min:1|max:100'
        ];
    }

    protected function RPS_Segment_getFormAttributes()
    {
        return [
            'MPN' => 'Manufacturer Full Length Part No.',
            'MFR' => 'Manufacturer Code',
            'MFN' => 'Manufacturer Name',
            'SER' => 'Part Serial No.',
            'PNR' => 'Part Number',
            'OPN' => 'Overlength Part No.',
            'USN' => 'Universal Serial No.',
            'ASN' => 'Airline Stock No.',
            'UCN' => 'Unique Component Id No.',
            'SPL' => 'Supplier Code',
            'UST' => 'Universal Serial Tracking No.',
            'PDT' => 'Part Description'
        ];
    }

    /**
     |-------------
     | Misc_Segment
     |-------------
     */

    protected function Misc_Segment_isPresent()
    {
        return false;
    }

    protected function Misc_Segment_getName()
    {
        return NULL;
    }

    protected function Misc_Segment_isMandatory()
    {
        return NULL;
    }

    protected function Misc_Segment_getFormInputs()
    {
        return [];
    }

    protected function Misc_Segment_getValidationRules($id = NULL)
    {
        return [];
    }

    protected function Misc_Segment_getFormAttributes()
    {
        return [];
    }

    /**
     * Export the Misc_Segment data.
     *
     * @return array
     */
    protected function Misc_Segment_export()
    {
        // Get raw attributes array.
        $attributes = $this->segment->getAttributes();

        $array = json_decode($attributes['values'], true) ?? [];

        return $array;
    }
}