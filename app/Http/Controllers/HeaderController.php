<?php

namespace App\Http\Controllers;

use App\Alert;
use App\CageCode;
use App\Customer;
use App\Location;
use Carbon\Carbon;
use App\HDR_Segment;
use App\Spec2kInput;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\HeaderRequest;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Notification;

class HeaderController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $report = ShopFinding::with('HDR_Segment')
            ->find($notification->get_RCS_SFI());
        
        $header = $report->HDR_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('HDR_Segment', $header, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($header, HDR_Segment::class) ? route('header.destroy', $header->id) : NULL;
        
        return view('header.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $header)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\HeaderRequest  $request
     * @param  \App\HDR_Segment  $header
     * @return \Illuminate\Http\Response
     */
    public function update(HeaderRequest $request, HDR_Segment $header)
    {
        $notification = Notification::findOrFail((string) $request->rcsSFI);
        
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        HDR_Segment::createOrUpdateSegment($request->all(), $shopFinding->id);
        
        return redirect(route('header.edit', $shopFinding->id))
            ->with(Alert::success('Header saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = HDR_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
    
    /**
     * Get an array of Airframe Manufacturers based on text input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAutocomplete(Request $request)
    {
        $element = $request->get('element');
        $term = $request->get('term');
        
        $ROC = $request->get('roc') ?? NULL;
        $RON = $request->get('ron') ?? NULL;
        
        $organisations = Location::getReportingOrganisation($ROC, $RON);
        
        if ($element) {
            if ($element == 'ROC') {
                
                $ROC = [];
                
                if (count($organisations)) {
                    foreach ($organisations as $organisation) {
                        $codes = $organisation->cage_codes->pluck('cage_code')->toArray();
                        
                        foreach ($codes as $code) {
                            $ROC[] = $code;
                        }
                    }
                }
                
                return response()->json($ROC, 200);
            } elseif ($element == 'RON') {
                $RON = array_unique($organisations->pluck('name')->toArray());
                return response()->json($RON, 200);
            }
        }
    }
    
    /**
     * Get reporting organisation info for autocomplete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReportingOrganisation(Request $request)
    {
        $roc = $request->get('roc') ?? NULL;
        $ron = $request->get('ron') ?? NULL;
        
        $organisation = Location::getReportingOrganisation($roc, $ron);
        
        if ($organisation->count() == 1) {
            return response()->json($organisation[0]->toArray(), 200);
        }
    }
    
    /**
     * Get an array of Customers based on text input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomersAutocomplete(Request $request)
    {
        $element = $request->get('element');
        $term = $request->get('term');
        
        $OPR = $request->get('opr') ?? NULL;
        $WHO = $request->get('who') ?? NULL;
        
        $customers = Customer::getCustomers($OPR, $WHO);
        
        if (count($customers) && $element) {
            if ($element == 'OPR') {
                $OPR = array_unique($customers->pluck('icao')->toArray());
                return response()->json($OPR, 200);
            } elseif ($element == 'WHO') {
                $WHO = array_unique($customers->pluck('company_name')->toArray());
                return response()->json($WHO, 200);
            }
        }
    }
    
    /**
     * Get single customer for autocomplete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCustomer(Request $request)
    {
        $OPR = $request->get('opr') ?? NULL;
        $WHO = $request->get('who') ?? NULL;
        
        $customer = Customer::getCustomer($OPR, $WHO);
        
        if ($customer->count() == 1) {
            return response()->json($customer[0]->toArray(), 200);
        }
    }
}
