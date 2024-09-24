<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\ValidationProfiler;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\LNK_Segment;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\LinkingFieldRequest;

class LinkingFieldController extends Controller
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
            ->with('ShopFindingsDetail.LNK_Segment')
            ->find($notification->get_RCS_SFI());
            
        $linkingField = $report->ShopFindingsDetail->LNK_Segment ?? $notification;
        
        $profiler = new ValidationProfiler('LNK_Segment', $linkingField, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($linkingField, LNK_Segment::class) ? route('linking-field.destroy', $linkingField->id) : NULL;
        
        return view('linking-field.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $linkingField)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LinkingFieldRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(LinkingFieldRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        LNK_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);
        
        return redirect(route('linking-field.edit', $shopFinding->id))
            ->with(Alert::success('Linking Fields saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = LNK_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
