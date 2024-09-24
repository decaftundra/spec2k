<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\HDR_Segment;
use App\ValidationProfiler;
use App\PieceParts\PiecePart;
use App\PieceParts\NHS_Segment;
use App\ShopFindings\ShopFinding;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\Auth;
use App\ShopFindings\ShopFindingsDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\Http\Requests\NHS_SegmentRequest;

class NHS_SegmentController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Requests\NHS_SegmentRequest  $notification
     * @param  string  $piecePartDetailId
     * @return \Illuminate\Http\Response
     */
    public function edit(RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $nhsSegment = NHS_Segment::where('piece_part_detail_id', $piecePartDetailId)->first() ?? NotificationPiecePart::findOrFail($piecePartDetailId);
        
        $profiler = new ValidationProfiler('NHS_Segment', $nhsSegment, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($nhsSegment, NHS_Segment::class) ? route('next-higher-assembly.destroy', $nhsSegment->id) : NULL;
        
        return view('next-higher-assembly.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('piecePartDetailId', $piecePartDetailId)
            ->with('segment', $nhsSegment)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\NHS_SegmentRequest  $request
     * @param  \App\Http\Requests\NHS_SegmentRequest  $notification
     * @param  App\PieceParts\PiecePartDetail  $piecePartDetail
     * @return \Illuminate\Http\Response
     */
    public function update(NHS_SegmentRequest $request, RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePartDetail = PiecePartDetail::firstOrCreate(['id' => $piecePartDetailId, 'piece_part_id' => $piecePart->id]);
        
        NHS_Segment::createOrUpdateSegment($request->all(), $piecePartDetailId);
        
        return redirect(route('next-higher-assembly.edit', [$shopFinding->id, $piecePartDetailId]))
            ->with(Alert::success('Next Higher Assembly saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = NHS_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->forceDelete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
