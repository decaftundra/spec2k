<?php

namespace App\Http\Controllers;

use App\PartList;
use App\CageCode;
use App\Customer;
use App\Location;
use App\AircraftDetail;
use App\EngineDetail;
use App\Codes\ActionCode;
use Illuminate\Http\Request;
use App\PieceParts\PiecePart;
use App\Codes\RcsFailureCode;
use App\Codes\FaultFoundCode;
use App\Codes\ShopActionCode;
use App\Codes\FaultInducedCode;
use App\Codes\SupplierRemovalTypeCode;
use App\Notification;
use App\Codes\HardwareSoftwareFailureCode;
use App\Codes\RepairFinalActionIndicatorCode;
use App\Codes\FaultConfirmsAircraftMessageCode;
use App\Codes\FaultConfirmsReasonForRemovalCode;
use App\Codes\FaultConfirmsAircraftPartBiteMessageCode;

class InformationController extends Controller
{
    /**
     * Show a listing of customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerIndex(Request $request)
    {
        $orderByWhitelist = [
            'company' => 'company_name',
            'code' => 'icao'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'company';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $customers = Customer::search($search)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.customers')
            ->with('customers', $customers);
    }
    
    /**
     * Show a listing of locations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locationIndex(Request $request)
    {
        $orderByWhitelist = [
            'name' => 'name',
            'sap_name' => 'sap_location_name',
            'code' => 'plant_code',
            'timezone' => 'timezone'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'name';
        
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $locations = Location::search($search)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.locations')
            ->with('locations', $locations);
    }
    
    /**
     * Show a listing of cage codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cageCodeIndex(Request $request)
    {
        $orderByWhitelist = [
            'code' => 'cage_code',
            'info' => 'info'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'code';
        
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $cageCodes = CageCode::search($search)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.cage-codes')
            ->with('cageCodes', $cageCodes);
    }
    
    /**
     * Show a listing of aircraft details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function aircraftIndex(Request $request)
    {
        $orderByWhitelist = [
            'reg' => 'aircraft_fully_qualified_registration_no',
            'id' => 'aircraft_identification_no',
            'name' => 'manufacturer_name',
            'code' => 'manufacturer_code',
            'model' => 'aircraft_model_identifier',
            'series' => 'aircraft_series_identifier'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'reg';
        
        $manufacturerCode = $request->manufacturer_code ?? NULL;
        
        // Get codes for dropdown menu.
        $manufacturerCodes = AircraftDetail::getManufacturerCodesDropDown();
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $aircraftDetails = AircraftDetail::search($search)
            ->manufacturerCode($manufacturerCode)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.aircraft')
            ->with('manufacturerCodes', $manufacturerCodes)
            ->with('aircraftDetails', $aircraftDetails);
    }
    
    /**
     * A list of parts included or excluded for the user's location.
     * If the user is an admin display all location parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locationPartsIndex(Request $request)
    {
        $defaultOrder = 'asc';
        $order = $defaultOrder;
        $partListsDropDown = [];
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        // Allow data admin users to select location.
        $locationId = auth()->user()->isDataAdmin() ? $request->location_id : auth()->user()->location->id;
        
        $partList = PartList::where('location_id', $locationId)->first();
        
        if ($partList) {
            $parts = collect($partList->getParts())->sort();
        
            if ($search) {
                $parts = $parts->filter(function($value, $key) use ($search) {
                    return stristr($value, $search) !== false;
                });
            }
            
            if ($request->order == 'desc') {
                $parts = $parts->reverse();
            }
            
            $parts = $parts->paginate(20);
            
            $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        } else {
            $parts = [];
        }
        
        if (auth()->user()->isDataAdmin()) {
            $partListsDropDown = Location::whereHas('part_list')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
            $partListsDropDown = array_replace(['' => 'Please select...'], $partListsDropDown);
        }
        
        $request->flash();
        
        return view('info.location-parts')
            ->with('partList', $partList)
            ->with('parts', $parts)
            ->with('partListsDropDown', $partListsDropDown);
    }
    
    /**
     * NOT CURRENTLY USED...
     * Show a listing of Fribourg parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fribourgPartsIndex(Request $request)
    {
        $defaultOrder = 'asc';
        $order = $defaultOrder;
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $parts = collect(Notification::$fribourgParts)->sort();
        
        if ($search) {
            $parts = $parts->filter(function($value, $key) use ($search) {
                return stristr($value, $search) !== false;
            });
        }
        
        if ($request->order == 'desc') {
            $parts = $parts->reverse();
        }
        
        $parts = $parts->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.fribourg-parts')->with('parts', $parts);
    }
    
    /**
     * Show a lising of Received LRU Failure Codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rcsFailureCodesIndex(Request $request)
    {
        $warnings = PiecePart::$warnings;
        
        $rrcCodes = SupplierRemovalTypeCode::getDropDownValues();
        $ffcCodes = FaultFoundCode::getDropDownValues();
        $ffiCodes = FaultInducedCode::getDropDownValues();
        $fhsCodes = HardwareSoftwareFailureCode::getDropDownValues();
        $fcrCodes = FaultConfirmsReasonForRemovalCode::getDropDownValues();
        $facCodes = FaultConfirmsAircraftMessageCode::getDropDownValues();
        $fbcCodes = FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues();
        
        $orderByWhitelist = [
            'rrc' => 'RRC',
            'ffc' => 'FFC',
            'ffi' => 'FFI',
            'fhs' => 'FHS',
            'fcr' => 'FCR',
            'fac' => 'FAC',
            'fbc' => 'FBC',
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'rrc';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $rrc = $request->rrc ?? NULL;
        $ffc = $request->ffc ?? NULL;
        $ffi = $request->ffi ?? NULL;
        $fhs = $request->fhs ?? NULL;
        $fcr = $request->fcr ?? NULL;
        $fac = $request->fac ?? NULL;
        $fbc = $request->fbc ?? NULL;
        
        $codes = RcsFailureCode::rrc($rrc)
            ->ffc($ffc)
            ->ffi($ffi)
            ->fhs($fhs)
            ->fcr($fcr)
            ->fac($fac)
            ->fbc($fbc)
            ->orderBy($orderBy, $order)
            ->paginate(20);
            
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
            
        return view('info.rcs-failure-codes')
            ->with('rrc', $rrcCodes)
            ->with('ffc', $ffcCodes)
            ->with('ffi', $ffiCodes)
            ->with('fhs', $fhsCodes)
            ->with('fcr', $fcrCodes)
            ->with('fac', $facCodes)
            ->with('fbc', $fbcCodes)
            ->with('warnings', $warnings)
            ->with('codes', $codes);
    }
    
    /**
     * Show a listing of Shop Action Codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shopActionCodesIndex(Request $request)
    {
        $warnings = PiecePart::$warnings;
        
        $sacCodes = ShopActionCode::getDropDownValues();
        $rfiCodes = RepairFinalActionIndicatorCode::getDropDownValues();
        
        $orderByWhitelist = [
            'sac' => 'SAC',
            'rfi' => 'RFI',
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'sac';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $sac = $request->sac ?? NULL;
        $rfi = $request->rfi ?? NULL;
        
        $codes = ActionCode::rfi($rfi)
            ->sac($sac)
            ->orderBy($orderBy, $order)
            ->paginate(20);
            
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
            
        return view('info.shop-action-codes')
            ->with('sac', $sacCodes)
            ->with('rfi', $rfiCodes)
            ->with('codes', $codes);
    }
    
    /**
     * Show the table of user roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userRolesIndex(Request $request)
    {
        return view('info.user-roles');
    }
    
    /**
     * Show orderable, searcheable list of engine details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function engineDetailsIndex(Request $request)
    {
        $orderByWhitelist = [
            'manufacturer' => 'engine_manufacturer',
            'code' => 'engine_manufacturer_code',
            'type' => 'engine_type',
            'series' => 'engines_series'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'manufacturer';
        
        $manufacturerCode = $request->engine_manufacturer_code ?? NULL;
        
        // Get codes for dropdown menu.
        $manufacturerCodes = EngineDetail::getManufacturerCodesDropDown();
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $engineDetails = EngineDetail::search($search)
            ->manufacturerCode($manufacturerCode)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('info.engine-details')
            ->with('manufacturerCodes', $manufacturerCodes)
            ->with('engineDetails', $engineDetails);
    }
}
