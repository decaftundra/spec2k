<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\ValidationProfiler;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ATT_Segment;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\AccumulatedTimeTextRequest;

class AccumulatedTimeTextController extends Controller
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
        
        $report = ShopFinding::with('HDR_Segment')->with('ShopFindingsDetail.ATT_Segment')
            ->find($notification->get_RCS_SFI());
            
        $accumulatedTimeText = $report->ShopFindingsDetail->ATT_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('ATT_Segment', $accumulatedTimeText, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($accumulatedTimeText, ATT_Segment::class) ? route('accumulated-time-text.destroy', $accumulatedTimeText->id) : NULL;
        
        return view('accumulated-time-text.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $accumulatedTimeText)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AccumulatedTimeTextRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(AccumulatedTimeTextRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        ATT_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
            
        return redirect(route('accumulated-time-text.edit', $shopFinding->id))
            ->with(Alert::success('Accumulated Time Text saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = ATT_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
