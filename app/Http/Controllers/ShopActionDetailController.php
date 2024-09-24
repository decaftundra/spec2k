<?php

namespace App\Http\Controllers;

use Log;
use App\Alert;
use App\Spec2kInput;
use App\Codes\ActionCode;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\Codes\ShopActionCode;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\SAS_Segment;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\ShopActionDetailRequest;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Settings;

class ShopActionDetailController extends Controller
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
            ->with('ShopFindingsDetail.SAS_Segment')
            ->find($notification->get_RCS_SFI());

        $shopActionDetail = $report->ShopFindingsDetail->SAS_Segment ?? $notification;

        $SAC = ShopActionCode::getDropDownValues(false);
        $RFI = [true, false, NULL];

        $profiler = new ValidationProfiler('SAS_Segment', $shopActionDetail, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();

        $deleteRoute = is_a($shopActionDetail, SAS_Segment::class) ? route('shop-action-details.destroy', $shopActionDetail->id) : NULL;

        return view('shop-action-details.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('segment', $shopActionDetail)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory)
            ->with('RFI', $RFI)
            ->with('SAC', $SAC);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ShopActionDetailRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(ShopActionDetailRequest $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);

        $shopFinding = ShopFinding::firstOrCreate(['id' => $request->rcsSFI], ['plant_code' => $request->plant_code]);

        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);


        // LJMJun23 MGTSUP-504
        if (app()->runningInConsole())
        {
//            Log::info("LJMDEBUG11 update ShopAction From Console");
        }
        else            {
            if ($request->all()['SAC'] == 'SCRP')
            {        // so do the checks in here about can you set to SCRP?

//                Log::info("LJMDEBUG11 update ShopAction to SCRP From Browser");

                // WHAT DOES SAP SAY (attached to MGTSUP-504)
                if ($notification->shipped_at != NULL)
                {
                    return redirect(route('shop-action-details.edit', $shopFinding->id))
                                ->with(Alert::error('You cannot set to SCRP because SAP says it was shipped.', true));
                }
            }
            else
            { // if setting from SCRP do we have a SCRP date in SAP?
                if (($notification->scrapped_at != NULL) && ($notification->shipped_at == NULL))
                {
                    return redirect(route('shop-action-details.edit', $shopFinding->id))
                                ->with(Alert::error('You cannot set to anything other than SCRP because SAP says it was scrapped.', true));
                }
            }
        }




        SAS_Segment::createOrUpdateSegment($request->all(), $shopFindingsDetail->id);




        return redirect(route('shop-action-details.edit', $shopFinding->id))
            ->with(Alert::success('Shop Action Details saved successfully!'));
    }

    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = SAS_Segment::findOrFail($id);

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
    public function getActionCodes(Request $request)
    {
        $data = $request->except(['_token']);

        Log::info($data);

        // Fix javascript boolean weirdness.
        if ($data['RFI'] == 'true') {
            $RFI = true;
        } else if ($data['RFI'] == 'false') {
            $RFI = false;
        } else if ($data['RFI'] == '1') {
            $RFI = true;
        } else if ($data['RFI'] == '0') {
            $RFI = false;
        } else {
            $RFI = null;
        }

        if ($data['SAC'] == 'null') {
            $SAC = null;
        } else {
            $SAC = $data['SAC'];
        }

        $codes = ActionCode::getActionCodes($RFI, $SAC)->toArray();

        Log::info($codes);

        return response()->json(['success' => 'true', 'codes' => $codes, 'data' => $data], 200);
    }
}
