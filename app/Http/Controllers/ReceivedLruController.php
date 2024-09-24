<?php

namespace App\Http\Controllers;

use App\Alert;
use Carbon\Carbon;
use App\Spec2kInput;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\Codes\RcsFailureCode;
use App\Codes\FaultFoundCode;
use App\Codes\FaultInducedCode;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\RCS_Segment;
use Illuminate\Support\Facades\Auth;
use App\Codes\SupplierRemovalTypeCode;
use App\ValidationProfiles\UtasProfile;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\ReceivedLruRequest;
use App\Codes\HardwareSoftwareFailureCode;
use App\Codes\FaultConfirmsAircraftMessageCode;
use App\Codes\FaultConfirmsReasonForRemovalCode;
use App\Codes\FaultConfirmsAircraftPartBiteMessageCode;

class ReceivedLruController extends Controller
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
            ->with('ShopFindingsDetail.RCS_Segment')
            ->find($notification->get_RCS_SFI());
            
        $receivedLru = $report->ShopFindingsDetail->RCS_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('RCS_Segment', $receivedLru, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $utas = $profiler->getMiscSegmentName() == UtasProfile::MISC_SEGMENT_NAME ? 1 : 0;
        
        $rrc = SupplierRemovalTypeCode::getDropDownValues(false);
        $ffc = FaultFoundCode::getDropDownValues(false);
        $ffi = FaultInducedCode::getDropDownValues(false);
        $fhs = HardwareSoftwareFailureCode::getDropDownValues(false);
        $fcr = FaultConfirmsReasonForRemovalCode::getDropDownValues(false);
        $fac = FaultConfirmsAircraftMessageCode::getDropDownValues(false);
        $fbc = FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(false);
        
        $deleteRoute = is_a($receivedLru, RCS_Segment::class) ? route('received-lru.destroy', $receivedLru->id) : NULL;
        
        return view('received-lru.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $receivedLru)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory)
            ->with('utas', $utas)
            ->with('rrc', $rrc)
            ->with('ffc', $ffc)
            ->with('ffi', $ffi)
            ->with('fhs', $fhs)
            ->with('fcr', $fcr)
            ->with('fac', $fac)
            ->with('fbc', $fbc);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ReceivedLruRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(ReceivedLruRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        RCS_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
            
        return redirect(route('received-lru.edit', $shopFinding->id))
            ->with(Alert::success('Received LRU saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = RCS_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
    
    /**
     * Get the filtered RCS failure codes via ajax.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRcsFailureCodes(Request $request)
    {
        $data = $request->except(['_token']);
        
        $utas = $data['utas'] ?? NULL;
        $rrc = $data['rrc'] ?? NULL;
        $ffc = $data['ffc'] ?? NULL;
        $ffi = $data['ffi'] ?? NULL;
        $fhs = $data['fhs'] ?? NULL;
        $fcr = $data['fcr'] ?? NULL;
        $fac = $data['fac'] ?? NULL;
        $fbc = $data['fbc'] ?? NULL;
        
        $codes = RcsFailureCode::getRcsFailureCodes($utas, $rrc, $ffc, $ffi, $fhs, $fcr, $fac, $fbc)->toArray();
        
        return response()->json(['success' => 'true', 'codes' => $codes, 'data' => $data], 200);
    }
}
