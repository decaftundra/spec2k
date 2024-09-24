<?php

namespace App\Http\Controllers;

use App\Alert;
use Carbon\Carbon;
use Kfirba\QueryGenerator;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\ShopFinding;
use App\NotificationPiecePart;
use Illuminate\Support\Facades\DB;
use App\Events\PiecePartsBatchSave;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\Auth;
use App\Events\PiecePartsBatchCreated;
use App\Events\PiecePartsBatchUpdated;
use App\Interfaces\RCS_SegmentInterface;
use App\ShopFindings\ShopFindingsDetail;
use App\Http\Requests\WPS_SegmentRequest;
use Illuminate\Support\Facades\Validator;
use App\Codes\PrimaryPiecePartFailureIndicator;

class PiecePartController extends Controller
{
    /**
     * The form inputs for the WPS Segments.
     *
     * @var array
     */
    public $wpsformInputs;
    
    /**
     * The WPS Segment validation profiler.
     *
     * @var \App\ValidationProfiler
     */
    public $wpsprofiler;
    
    /**
     * The NHS Segment validation profiler.
     *
     * @var \App\ValidationProfiler
     */
    public $nhsprofiler;
    
    /**
     * The RPS Segment validation profiler.
     *
     * @var \App\ValidationProfiler
     */
    public $rpsprofiler;
    
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Interfaces\RCS_SegmentInterface $notification
     * @return \Illuminate\Http\Response
     */
    public function index(RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $piecePart = PiecePart::with('HDR_Segment')
            ->with('PiecePartDetails.WPS_Segment')
            ->where('shop_finding_id', $notification->get_RCS_SFI())
            ->first();
        
        // Retrieve piece parts from Notification AND in-app created piece parts.
        $notificationPieceParts = $notification->PieceParts;
        $savedPieceParts = $piecePart && $piecePart->PiecePartDetails ? $piecePart->PiecePartDetails : [];
        $piecePartDetails = PiecePart::getPiecePartDetails($notification->get_RCS_SFI());
        
        // Check whether to display Piece Part Fail ID warning.
        $warning = PiecePart::getPiecePartWarningMessage($notification->get_RCS_SFI());
        
        $allInputs = [];
        
        if (count($piecePartDetails)) {
            foreach ($piecePartDetails as $piecePartDetail) {
                $wpsSegment = WPS_Segment::where('piece_part_detail_id', $piecePartDetail['WPS_PPI'])->first()
                    ?? NotificationPiecePart::find($piecePartDetail['WPS_PPI']);
                
                // Assuming all segments will use the same validation and inputs.
                if (!$this->wpsformInputs  && $wpsSegment) {
                    $this->wpsprofiler = new ValidationProfiler('WPS_Segment', $wpsSegment, $notification->get_RCS_SFI());
                    $this->wpsformInputs = $this->wpsprofiler->getFormInputs();
                }
                
                if ($wpsSegment) {
                    $allInputs[$piecePartDetail['WPS_PPI']]['id'] = $piecePartDetail['WPS_PPI'];
                    $allInputs[$piecePartDetail['WPS_PPI']]['WPS_Segment'] = $wpsSegment;
                    $allInputs[$piecePartDetail['WPS_PPI']]['wps_inputs'] = $this->wpsformInputs;
                }
            }
        }
        
        $collection = collect($allInputs);
        
        return view('piece-parts.index')
            ->with('notificationId', $notification->get_RCS_SFI())
            ->with('plantCode', $notification->plant_code)
            ->with('piecePartDetails', $collection)
            ->with('warning', $warning)
            ->with('pfcCodes', PrimaryPiecePartFailureIndicator::getDropDownValues(false));
    }
    
    /**
     * Batch update the specified resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interfaces\RCS_SegmentInterface $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RCS_SegmentInterface $notification)
    {
        $this->authorize('show', $notification);
        
        $errorsArray = [];
        $WPS_ErrorsArray = [];
        $NHS_ErrorsArray = [];
        $RPS_ErrorsArray = [];
        
        $shopFindingId = $notification->get_RCS_SFI();
        $shopFinding = ShopFinding::firstOrCreate(['id' => $shopFindingId], ['plant_code' => $notification->plant_code]);
        
        if (count($request->except(['_token']))) {
            $WPS_ErrorsArray = $this->updateWPSSegments($request->except(['_token']), $shopFindingId);
            $NHS_ErrorsArray = $this->createNHSSegments($shopFindingId);
            $RPS_ErrorsArray = $this->createRPSSegments($shopFindingId);
        }
        
        // Fire one big event to set validation on all piece parts and shop finding.
        event(new PiecePartsBatchSave($shopFinding));
        
        $errorsArray = array_replace($WPS_ErrorsArray, $NHS_ErrorsArray, $RPS_ErrorsArray);
        
        if (!empty($errorsArray)) {
            return redirect(route('piece-parts.index', $notification->get_RCS_SFI()))
                ->with('errorsArray', $errorsArray)
                ->with(Alert::error('Some Piece Parts contained errors, please see below.'));
        }
        
        return redirect(route('piece-parts.index', $notification->get_RCS_SFI()))
            ->with(Alert::success('All Piece Parts saved successfully!'));
    }
    
    /**
     * Batch 'insert on duplicate key update' WPS Segments.
     *
     * @param  array  $data
     * @param  string  $shopFindingId
     * @return array $errorsArray
     */
    protected function updateWPSSegments($data, $shopFindingId)
    {
        $errorsArray = [];
        $allWPS_Segments = [];
        $all_PiecePartDetails = [];
        $all_Ids = [];
        
        $alreadyExistingIds = WPS_Segment::pluck('piece_part_detail_id')->toArray();
        
        if (count($data)) {
            foreach ($data as $ppi => $req) {
                $piecePartDetailId = $ppi;
                
                $req['PPI'] = (string) $req['PPI']; // Convert PPI to string.
                
                $now = Carbon::now();
                    
                $WPS_Segment = new WPS_Segment;
                $WPS_Segment->piece_part_detail_id = $piecePartDetailId;
                $WPS_Segment->PPI = $piecePartDetailId;
                $WPS_Segment->SFI = $shopFindingId;
                $WPS_Segment->PFC = $req['PFC'] ?? NULL;
                $WPS_Segment->MPN = $req['MPN'] ?? NULL;
                $WPS_Segment->MFR = $req['MFR'] ?? NULL;
                $WPS_Segment->SER = $req['SER'] ?? NULL;
                $WPS_Segment->MFN = $req['MFN'] ?? NULL;
                $WPS_Segment->FDE = $req['FDE'] ?? NULL;
                $WPS_Segment->PNR = $req['PNR'] ?? NULL;
                $WPS_Segment->USN = $req['USN'] ?? NULL;
                $WPS_Segment->OPN = $req['OPN'] ?? NULL;
                $WPS_Segment->PDT = $req['PDT'] ?? NULL;
                $WPS_Segment->GEL = $req['GEL'] ?? NULL;
                $WPS_Segment->MRD = !empty($req['MRD']) ? Carbon::createFromFormat('d/m/Y', $req['MRD']) : NULL;
                $WPS_Segment->ASN = $req['ASN'] ?? NULL;
                $WPS_Segment->UCN = $req['UCN'] ?? NULL;
                $WPS_Segment->SPL = $req['SPL'] ?? NULL;
                $WPS_Segment->UST = $req['UST'] ?? NULL;
                $WPS_Segment->created_at = $now;
                $WPS_Segment->updated_at = $now;
                $WPS_Segment->deleted_at = NULL;
                
                if (!$this->wpsprofiler) {
                    $this->wpsprofiler = new ValidationProfiler('WPS_Segment', $WPS_Segment, $shopFindingId);
                }
                
                // Assume all piece parts use the same validation profile.
                $validator = Validator::make($req, $this->wpsprofiler->getValidationRules($piecePartDetailId));
                $validatedConditionally = $this->wpsprofiler->conditionalValidation($validator);
                $valid = $validatedConditionally->fails() ? false : true;
                
                if ($valid) {
                    $allWPS_Segments[] = $WPS_Segment->toArray();
                } else {
                    $errorsArray[$ppi] = $validatedConditionally->errors(); // Collect error messages.
                }
            }
            
            if (count($allWPS_Segments)) {
                DB::transaction(function () use($alreadyExistingIds, $all_PiecePartDetails, $allWPS_Segments, $all_Ids, $shopFindingId) {
                    
                    DB::disableQueryLog();
                    
                    $now = Carbon::now();
                    
                    $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    
                    foreach ($allWPS_Segments as $segment) {
                        $all_Ids[] = $segment['piece_part_detail_id'];
                        
                        $piecePartDetailArray = [];
                        $piecePartDetailArray['id'] = $segment['piece_part_detail_id'];
                        $piecePartDetailArray['piece_part_id'] = $piecePart->id;
                        $piecePartDetailArray['created_at'] = $now;
                        $piecePartDetailArray['updated_at'] = $now;
                        $piecePartDetailArray['deleted_at'] = NULL;
                        
                        $all_PiecePartDetails[] = $piecePartDetailArray;
                    }
                    
                    $excludedColumnsFromUpdate = ['deleted_at', 'created_at'];
                    
                    // Mass 'insert on duplicate key update Piece Part' Details.
                    $queryObject = (new QueryGenerator)->generate('piece_part_details', $all_PiecePartDetails, $excludedColumnsFromUpdate);
                    DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    
                    // Mass 'insert on duplicate key update Piece Part' wps_segments.
                    $queryObject = (new QueryGenerator)->generate('wps_segments', $allWPS_Segments, $excludedColumnsFromUpdate);
                    DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    
                    $createdIds = array_diff($all_Ids, $alreadyExistingIds);
                    $updatedIds = array_intersect($all_Ids, $alreadyExistingIds);
                    $userId = Auth::id();
                    
                    // Create the activities, WPS_Segments differ from other piece parts in that we use the piece part id as the primary key.
                    
                    if (count($createdIds)) {
                        // Create Activities for all WPS Segments created.
                        event(new PiecePartsBatchCreated(WPS_Segment::class, $shopFindingId, $userId, $createdIds));
                    }
                    
                    if (count($updatedIds)) {
                        // Create Activities for all WPS Segments updated.
                        event(new PiecePartsBatchUpdated(WPS_Segment::class, $shopFindingId, $userId, $updatedIds));
                    }
                    
                    DB::enableQueryLog();
                });
            }
        }
        
        return $errorsArray;
    }
    
    /**
     * Batch save NEW NHS Segments from SAP.
     * This function does NOT update existing NHS Segments.
     *
     * @param  string  $shopFindingId
     * @return array $errorsArray
     */
    protected function createNHSSegments($shopFindingId)
    {
        $errorsArray = [];
        $all_NHS_Segments = [];
        $all_PiecePartDetails = [];
        $all_Ids = [];
        
        // Get all notification NHS Segments.
        $all_SAP_PieceParts = NotificationPiecePart::where('wpsSFI', $shopFindingId)->get();
        
        if (count($all_SAP_PieceParts)) { // Chunk???
            
            // We only need to save segments that have not yet been saved from SAP.
            foreach ($all_SAP_PieceParts as $SAP_piecePart) {
                $exists = NHS_Segment::where('piece_part_detail_id', $SAP_piecePart->wpsPPI)->first();
                
                if (!$exists) {
                    $NHS_Segment = new NHS_Segment;
                    $NHS_Segment->MFR = $SAP_piecePart->get_NHS_MFR();
                    $NHS_Segment->SER = $SAP_piecePart->get_NHS_SER();
                    $NHS_Segment->MPN = $SAP_piecePart->get_NHS_MPN();
                    $NHS_Segment->MFN = $SAP_piecePart->get_NHS_MFN();
                    $NHS_Segment->PNR = $SAP_piecePart->get_NHS_PNR();
                    $NHS_Segment->OPN = $SAP_piecePart->get_NHS_OPN();
                    $NHS_Segment->USN = $SAP_piecePart->get_NHS_USN();
                    $NHS_Segment->PDT = $SAP_piecePart->get_NHS_PDT();
                    $NHS_Segment->ASN = $SAP_piecePart->get_NHS_ASN();
                    $NHS_Segment->UCN = $SAP_piecePart->get_NHS_UCN();
                    $NHS_Segment->SPL = $SAP_piecePart->get_NHS_SPL();
                    $NHS_Segment->UST = $SAP_piecePart->get_NHS_UST();
                    $NHS_Segment->NPN = $SAP_piecePart->get_NHS_NPN();
                    
                    if (!$this->nhsprofiler) {
                        $this->nhsprofiler = new ValidationProfiler('NHS_Segment', $NHS_Segment, $shopFindingId);
                    }
                    
                    $validator = Validator::make($NHS_Segment->toArray(), $this->nhsprofiler->getValidationRules($SAP_piecePart->wpsPPI));
                    $validatedConditionally = $this->nhsprofiler->conditionalValidation($validator);
                    $valid = $validatedConditionally->fails() ? false : true;
                    
                    if ($valid) {
                        $now = Carbon::now();
                        
                        $NHS_Segment->piece_part_detail_id = $SAP_piecePart->wpsPPI;
                        $NHS_Segment->created_at = $now;
                        $NHS_Segment->updated_at = $now;
                        $NHS_Segment->deleted_at = NULL;
                        
                        $all_NHS_Segments[] = $NHS_Segment->toArray();
                    } else {
                        $errorsArray[$SAP_piecePart->wpsPPI] = $validatedConditionally->errors(); // Collect error messages.
                    }
                }
            }
            
            if (count($all_NHS_Segments)) {
                DB::transaction(function () use($all_PiecePartDetails, $all_NHS_Segments, $all_Ids, $shopFindingId) {
                    
                    DB::disableQueryLog();
                    
                    $now = Carbon::now();
                    
                    $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    
                    foreach ($all_NHS_Segments as $segment) {
                        $all_Ids[] = $segment['piece_part_detail_id'];
                        
                        $piecePartDetailArray = [];
                        $piecePartDetailArray['id'] = $segment['piece_part_detail_id'];
                        $piecePartDetailArray['piece_part_id'] = $piecePart->id;
                        $piecePartDetailArray['created_at'] = $now;
                        $piecePartDetailArray['updated_at'] = $now;
                        $piecePartDetailArray['deleted_at'] = NULL;
                        
                        $all_PiecePartDetails[] = $piecePartDetailArray;
                    }
                    
                    $excludedColumnsFromUpdate = ['deleted_at', 'created_at'];
                    
                    // Mass 'insert on duplicate key update Piece Part' Details.
                    $queryObject = (new QueryGenerator)->generate('piece_part_details', $all_PiecePartDetails, $excludedColumnsFromUpdate);
                    DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    
                    // Mass insert NHS Segments.
                    DB::table('NHS_Segments')->insert($all_NHS_Segments);
                    
                    $newIds = NHS_Segment::whereIn('piece_part_detail_id', $all_Ids)->pluck('id')->toArray();
                    
                    // Create Activities for all NHS Segments created.
                    event(new PiecePartsBatchCreated(NHS_Segment::class, $shopFindingId, Auth::id(), $newIds));
                    
                    DB::enableQueryLog();
                });
            }
        }
        
        return $errorsArray;
    }
    
    /**
     * Batch save NEW RPS Segments from SAP.
     * This function does NOT update existing RPS Segments.
     *
     * @param  string  $shopFindingId
     * @return array $errorsArray
     */
    protected function createRPSSegments($shopFindingId)
    {
        $errorsArray = [];
        $all_RPS_Segments = [];
        $all_PiecePartDetails = [];
        $all_Ids = [];
        
        // Get all notification NHS Segments.
        $all_SAP_PieceParts = NotificationPiecePart::where('wpsSFI', $shopFindingId)->get();
        
        if (count($all_SAP_PieceParts)) { // Chunk???
            
            // We only need to save segments that have not yet been saved from SAP.
            foreach ($all_SAP_PieceParts as $SAP_piecePart) {
                $exists = RPS_Segment::where('piece_part_detail_id', $SAP_piecePart->wpsPPI)->first();
                
                if (!$exists) {
                    $RPS_Segment = new RPS_Segment;
                    $RPS_Segment->MPN = $SAP_piecePart->get_RPS_MPN();
                    $RPS_Segment->MFR = $SAP_piecePart->get_RPS_MFR();
                    $RPS_Segment->MFN = $SAP_piecePart->get_RPS_MFN();
                    $RPS_Segment->PNR = $SAP_piecePart->get_RPS_PNR();
                    $RPS_Segment->OPN = $SAP_piecePart->get_RPS_OPN();
                    $RPS_Segment->SER = $SAP_piecePart->get_RPS_SER();
                    $RPS_Segment->USN = $SAP_piecePart->get_RPS_USN();
                    $RPS_Segment->ASN = $SAP_piecePart->get_RPS_ASN();
                    $RPS_Segment->UCN = $SAP_piecePart->get_RPS_UCN();
                    $RPS_Segment->SPL = $SAP_piecePart->get_RPS_SPL();
                    $RPS_Segment->UST = $SAP_piecePart->get_RPS_UST();
                    $RPS_Segment->PDT = $SAP_piecePart->get_RPS_PDT();
                    
                    if (!$this->rpsprofiler) {
                        $this->rpsprofiler = new ValidationProfiler('RPS_Segment', $RPS_Segment, $shopFindingId);
                    }
                    
                    $validator = Validator::make($RPS_Segment->toArray(), $this->rpsprofiler->getValidationRules($SAP_piecePart->wpsPPI));
                    $validatedConditionally = $this->rpsprofiler->conditionalValidation($validator);
                    $valid = $validatedConditionally->fails() ? false : true;
                    
                    if ($valid) {
                        $now = Carbon::now();
                        
                        $RPS_Segment->piece_part_detail_id = $SAP_piecePart->wpsPPI;
                        $RPS_Segment->created_at = $now;
                        $RPS_Segment->updated_at = $now;
                        $RPS_Segment->deleted_at = NULL;
                        
                        $all_RPS_Segments[] = $RPS_Segment->toArray();
                    } else {
                        $errorsArray[$SAP_piecePart->wpsPPI] = $validatedConditionally->errors(); // Collect error messages.
                    }
                }
            }
            
            if (count($all_RPS_Segments)) {
                DB::transaction(function () use($all_PiecePartDetails, $all_RPS_Segments, $all_Ids, $shopFindingId) {
                    
                    DB::disableQueryLog();
                    
                    $now = Carbon::now();
                    
                    $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFindingId]);
                    
                    foreach ($all_RPS_Segments as $segment) {
                        $all_Ids[] = $segment['piece_part_detail_id'];
                        
                        $piecePartDetailArray = [];
                        $piecePartDetailArray['id'] = $segment['piece_part_detail_id'];
                        $piecePartDetailArray['piece_part_id'] = $piecePart->id;
                        $piecePartDetailArray['created_at'] = $now;
                        $piecePartDetailArray['updated_at'] = $now;
                        $piecePartDetailArray['deleted_at'] = NULL;
                        
                        $all_PiecePartDetails[] = $piecePartDetailArray;
                    }
                    
                    $excludedColumnsFromUpdate = ['deleted_at', 'created_at'];
                    
                    // Mass 'insert on duplicate key update' Piece Part Details.
                    $queryObject = (new QueryGenerator)->generate('piece_part_details', $all_PiecePartDetails, $excludedColumnsFromUpdate);
                    DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    
                    // Mass insert NHS Segments.
                    DB::table('RPS_Segments')->insert($all_RPS_Segments);
                    
                    $newIds = RPS_Segment::whereIn('piece_part_detail_id', $all_Ids)->pluck('id')->toArray();
                    
                    // Create Activities for all RPS Segment created.
                    event(new PiecePartsBatchCreated(RPS_Segment::class, $shopFindingId, Auth::id(), $newIds));
                    
                    DB::enableQueryLog();
                });
            }
        }
        
        return $errorsArray;
    }
}
