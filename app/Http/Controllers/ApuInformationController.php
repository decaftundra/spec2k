<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\ValidationProfiler;
use App\ShopFindings\API_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\ApuInformationRequest;

class ApuInformationController extends Controller
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
            ->with('ShopFindingsDetail.API_Segment')
            ->find($notification->get_RCS_SFI());
            
        $apuInformation = $report->ShopFindingsDetail->API_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('API_Segment', $apuInformation, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = route('apu-information.destroy', $notification->get_RCS_SFI());
        
        $deleteRoute = is_a($apuInformation, API_Segment::class) ? route('apu-information.destroy', $apuInformation->id) : NULL;
        
        return view('apu-information.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $apuInformation)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ApuInformationRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(ApuInformationRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        API_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
        
        return redirect(route('apu-information.edit', $shopFinding->id))
            ->with(Alert::success('APU Information saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = API_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
