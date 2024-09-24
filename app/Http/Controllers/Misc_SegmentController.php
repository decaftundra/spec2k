<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\Codes\UtasCode;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\Codes\UtasReasonCode;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\Misc_Segment;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\Misc_SegmentRequest;

class Misc_SegmentController extends Controller
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
            ->with('ShopFindingsDetail.Misc_Segment')
            ->find($notification->get_RCS_SFI());
            
        $miscSegment = $report->ShopFindingsDetail->Misc_Segment ?? $notification;
            
        $header = $report && $report->Misc_Segment ? $report->Misc_Segment : $notification;
        
        $profiler = new ValidationProfiler('Misc_Segment', $miscSegment, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        $miscSegmentName = $profiler->getMiscSegmentName();
        $profileName = $profiler->getProfileName();
        
        $deleteRoute = is_a($miscSegment, Misc_Segment::class) ? route('misc-segment.destroy', $miscSegment->id) : NULL;
        
        return view('misc-segment.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $miscSegment)
            ->with('header', $header)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory)
            ->with('miscSegmentName', $miscSegmentName)
            ->with('profileName', $profileName);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Misc_SegmentRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Misc_SegmentRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        Misc_Segment::createOrUpdateSegment($request->except(['_token', 'source_data']), $shopFindingsDetail->id);
        
        return redirect(route('misc-segment.edit', $shopFinding->id))
            ->with(Alert::success('Misc segment saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = Misc_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
    
    /**
     * Get the filtered Utas codes via ajax.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function getUtasCodes(Request $request, RCS_SegmentInterface $notification)
    {
        $data = $request->except(['_token']);
        
        $plantCode = $data['plantCode'] ?? NULL;
        $subassemblyName = $data['subassemblyName'] ?? NULL;
        $component = $data['component'] ?? NULL;
        $feature = $data['feature'] ?? NULL;
        $description = $data['description'] ?? NULL;
        
        $RCS_Segment = RCS_Segment::where('SFI', $notification->get_RCS_SFI())->first();
        
        $partNo = $RCS_Segment ? $RCS_Segment->get_RCS_MPN() : NULL;
        
        $codes = UtasCode::getUtasCodes($plantCode, $partNo, $subassemblyName, $component, $feature, $description)->toArray();
        
        return response()->json(['success' => 'true', 'codes' => $codes, 'data' => $data], 200);
    }
    
    /**
     * Get the filtered Utas Reason codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function getUtasReasonCodes(Request $request, RCS_SegmentInterface $notification)
    {
        $data = $request->except(['_token']);
        
        $plant = $data['plant'] ?? NULL;
        $reason = $data['reason'] ?? NULL;
        
        $RCS_Segment = RCS_Segment::where('SFI', $notification->get_RCS_SFI())->first();
        
        $type = $RCS_Segment ? $RCS_Segment->get_RCS_RRC() : NULL;
        
        $codes = UtasReasonCode::getUtasReasonCodes($plant, $type, $reason)->toArray();
        
        return response()->json(['success' => 'true', 'codes' => $codes, 'data' => $data], 200);
    }
    
    /**
     * Get the RRC type code from the RCS_Segment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function getUtasTypeCode(Request $request, RCS_SegmentInterface $notification)
    {
        $RCS_Segment = RCS_Segment::where('SFI', $notification->get_RCS_SFI())->first();
        
        $type = $RCS_Segment ? $RCS_Segment->get_RCS_RRC() : NULL;
        
        return response()->json(['success' => 'true', 'type' => $type], 200);
    }
    
    /**
     * Get the MPN part no from the RCS_Segment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function getUtasPartNo(Request $request, RCS_SegmentInterface $notification)
    {
        $RCS_Segment = RCS_Segment::where('SFI', $notification->get_RCS_SFI())->first();
        
        $partNo = $RCS_Segment ? $RCS_Segment->get_RCS_MPN() : NULL;
        
        return response()->json(['success' => 'true', 'partNo' => $partNo], 200);
    }
}
