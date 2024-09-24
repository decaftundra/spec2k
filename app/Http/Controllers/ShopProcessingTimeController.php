<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\ValidationProfiler;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\SPT_Segment;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\ShopProcessingTimeRequest;

class ShopProcessingTimeController extends Controller
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
            ->with('ShopFindingsDetail.SPT_Segment')
            ->find($notification->get_RCS_SFI());
            
        $shopProcessingTime = $report->ShopFindingsDetail->SPT_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('SPT_Segment', $shopProcessingTime, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($shopProcessingTime, SPT_Segment::class) ? route('shop-processing-time.destroy', $shopProcessingTime->id) : NULL;
        
        return view('shop-processing-time.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $shopProcessingTime)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ShopProcessingTimeRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(ShopProcessingTimeRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        SPT_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
        
        return redirect(route('shop-processing-time.edit', $shopFinding->id))
            ->with(Alert::success('Shop Processing Time saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = SPT_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
