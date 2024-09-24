<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Spec2kInput;
use App\HDR_Segment;
use App\ValidationProfiler;
use App\PieceParts\PiecePart;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\ShopFinding;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartDetail;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\RPS_SegmentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RPS_SegmentController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @param  \App\PieceParts\PiecePartDetail  $piecePartDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $rpsSegment = RPS_Segment::where('piece_part_detail_id', $piecePartDetailId)->first() ?? NotificationPiecePart::findOrFail($piecePartDetailId);
        
        $profiler = new ValidationProfiler('RPS_Segment', $rpsSegment, $notification->get_RCS_SFI());
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($rpsSegment, RPS_Segment::class) ? route('replaced-piece-part.destroy', $rpsSegment->id) : NULL;
        
        return view('replaced-piece-part.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('piecePartDetailId', $piecePartDetailId)
            ->with('segment', $rpsSegment)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RPS_SegmentRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @param  string  $piecePartDetailId
     * @return \Illuminate\Http\Response
     */
    public function update(RPS_SegmentRequest $request, RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePartDetail = PiecePartDetail::firstOrCreate(['id' => $piecePartDetailId, 'piece_part_id' => $piecePart->id]);
        
        RPS_Segment::createOrUpdateSegment($request->all(), $piecePartDetailId);
        
        return redirect(route('replaced-piece-part.edit', [$shopFinding->id, $piecePartDetailId]))
            ->with(Alert::success('Replaced Piece Part saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = RPS_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->forceDelete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
