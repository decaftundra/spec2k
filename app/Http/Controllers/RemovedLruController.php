<?php

namespace App\Http\Controllers;

use App\Alert;
use Carbon\Carbon;
use App\Spec2kInput;
use App\ValidationProfiler;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\RLS_Segment;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\RemovedLruRequest;
use App\Interfaces\RCS_SegmentInterface;

class RemovedLruController extends Controller
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

        $report = ShopFinding::with('HDR_Segment')->with('ShopFindingsDetail.RLS_Segment')
            ->find($notification->get_RCS_SFI());










        $removedLru = $report->ShopFindingsDetail->RLS_Segment ?? $notification;

        $profiler = new ValidationProfiler('RLS_Segment', $removedLru, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();

        $deleteRoute = is_a($removedLru, RLS_Segment::class) ? route('removed-lru.destroy', $removedLru->id) : NULL;









        //// LJMJun23 MGTSUP-518 we should prefill the 3 top mandatory data values here if there is no RLS_Segment yet.
        if (($report == NULL) || (($report != NULL) && ($report->ShopFindingsDetail != NULL) && ($report->ShopFindingsDetail->RLS_Segment == NULL)))
        {
            // we need to change the form inputs here so that the page loads with the correct values.
//            if ($report->ShopFindingsDetail->RCS_Segment != NULL)
//            {
                $removedLru->rlsMFR = $notification->rcsMFR;
                $removedLru->rlsMPN = $notification->rcsMPN;
                $removedLru->rlsSER = $notification->rcsSER;
//            }
        }











        return view('removed-lru.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $removedLru)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RemovedLruRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(RemovedLruRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);

        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);

        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);

        RLS_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);

        return redirect(route('removed-lru.edit', $shopFinding->id))
            ->with(Alert::success('Removed LRU saved successfully!'));
    }

    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = RLS_Segment::findOrFail($id);

        $this->authorize('delete', $segment);

        if ($segment->delete()) {
            return response()->json(['success' => true], 200);
        }

        return response()->json(['error' => true], 500);
    }
}
