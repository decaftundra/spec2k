<?php

namespace App\Http\Controllers;

use App\Alert;
use Carbon\Carbon;
use App\Spec2kInput;
use App\HDR_Segment;
use App\ValidationProfiler;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\ShopFindings\ShopFinding;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\WPS_SegmentRequest;


class WPS_SegmentController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @param  string  $piecePartDetailId
     * @return \Illuminate\Http\Response
     */
    public function edit(RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $wpsSegment = WPS_Segment::where('piece_part_detail_id', $piecePartDetailId)->first() ?? NotificationPiecePart::find($piecePartDetailId);
        
        $profiler = new ValidationProfiler('WPS_Segment', $wpsSegment, $notification->get_RCS_SFI()); // Should we be passing in the piece part detail id ???
        $formInputs = Spec2kInput::convert($profiler->getFormInputs());
        $mandatory = $profiler->isMandatory();
        
        $deleteRoute = is_a($wpsSegment, WPS_Segment::class) ? route('worked-piece-part.destroy', $wpsSegment->PPI) : NULL;
        
        return view('worked-piece-part.edit')
            ->with('deleteRoute', $deleteRoute)
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('piecePartDetailId', $piecePartDetailId)
            ->with('segment', $wpsSegment)
            ->with('formInputs', $formInputs)
            ->with('mandatory', $mandatory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\WPS_SegmentRequest  $request
     * @param  \App\Interfaces\RCS_SegmentInterface  $notification
     * @param  string  $piecePartDetailId
     * @return \Illuminate\Http\Response
     */
    public function update(WPS_SegmentRequest $request, RCS_SegmentInterface $notification, $piecePartDetailId)
    {
        $this->authorize('show', $notification);
        
        $shopFinding = ShopFinding::firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePartDetail = PiecePartDetail::firstOrCreate(['id' => $piecePartDetailId, 'piece_part_id' => $piecePart->id]);
        
        WPS_Segment::createOrUpdateSegment($request->all(), $piecePartDetailId);
        
        return redirect(route('worked-piece-part.edit', [$shopFinding->id, $piecePartDetail->id]))
            ->with(Alert::success('Worked Piece Part saved successfully!'));
    }
    
    /**
     * Delete the resource from storage.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $segment = WPS_Segment::findOrFail($id);
        
        $this->authorize('delete', $segment);
        
        if ($segment->forceDelete()) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['error' => true], 500);
    }
}
