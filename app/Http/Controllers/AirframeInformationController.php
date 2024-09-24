<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\AircraftDetail;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\AID_Segment;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\AirframeInformationRequest;

class AirframeInformationController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $report = ShopFinding::with('HDR_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->find($notification->get_RCS_SFI());
            
        $airframeInformation = $report->ShopFindingsDetail->AID_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('AID_Segment', $airframeInformation, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($airframeInformation, AID_Segment::class) ? route('airframe-information.destroy', $airframeInformation->id) : NULL;
        
        return view('airframe-information.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $airframeInformation)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AirframeInformationRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(AirframeInformationRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        AID_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
        
        return redirect(route('airframe-information.edit', $shopFinding->id))
            ->with(Alert::success('Airframe Information saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = AID_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
    
    /**
     * Get an array of Airframe Manufacturers based on text input.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAutocomplete(Request $request)
    {
        $element = $request->get('element');
        $term = $request->get('term');
        
        $REG = $request->get('reg') ?? NULL;
        $AIN = $request->get('ain') ?? NULL;
        $MFN = $request->get('mfn') ?? NULL;
        $MFR = $request->get('mfr') ?? NULL;
        $AMC = $request->get('amc') ?? NULL;
        $ASE = $request->get('ase') ?? NULL;
        
        $aircraft = AircraftDetail::getAircraftDetail($REG, $AIN, $MFN, $MFR, $AMC, $ASE);
        
        // Send response if term is more than 1 character long.
        if (count($aircraft) && $element && (strlen($term) > 1)) {
            if ($element == 'REG') {
                $REG = array_unique($aircraft->pluck('aircraft_fully_qualified_registration_no')->toArray());
                return response()->json($REG, 200);
            } elseif ($element == 'AIN') {
                $AIN = array_unique($aircraft->pluck('aircraft_identification_no')->toArray());
                return response()->json($AIN, 200);
            } elseif ($element == 'MFN') {
                $MFN = array_unique($aircraft->pluck('manufacturer_name')->toArray());
                return response()->json($MFN, 200);
            } elseif ($element == 'MFR') {
                $MFR = array_unique($aircraft->pluck('manufacturer_code')->toArray());
                return response()->json($MFR, 200);
            } elseif ($element == 'AMC') {
                $AMC = array_unique($aircraft->pluck('aircraft_model_identifier')->toArray());
                return response()->json($AMC, 200);
            } elseif ($element == 'ASE') {
                $ASE = array_unique($aircraft->pluck('aircraft_series_identifier')->toArray());
                return response()->json($ASE, 200);
            }
        }
    }
    
    /**
     * Get aircraft detail record.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAircraftDetail(Request $request)
    {
        $REG = $request->get('reg') ?? NULL;
        $AIN = $request->get('ain') ?? NULL;
        $MFN = $request->get('mfn') ?? NULL;
        $MFR = $request->get('mfr') ?? NULL;
        $AMC = $request->get('amc') ?? NULL;
        $ASE = $request->get('ase') ?? NULL;
        
        $aircraft = AircraftDetail::getAircraftDetail($REG, $AIN, $MFN, $MFR, $AMC, $ASE);
        
        if ($aircraft->count() == 1) {
            return response()->json($aircraft[0]->toArray(), 200);
        }
    }
}
