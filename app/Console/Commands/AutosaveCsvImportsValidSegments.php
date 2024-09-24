<?php

namespace App\Console\Commands;

use App\HDR_Segment;
use App\Notification;
use App\NotificationPiecePart;
use App\PieceParts\NHS_Segment;
use App\PieceParts\PiecePart;
use App\PieceParts\PiecePartSegment;
use App\PieceParts\PiecePartDetail;
use App\PieceParts\RPS_Segment;
use App\PieceParts\WPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\SUS_Segment;
use App\Spec2kInput;
use App\ValidationProfiler;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AutosaveCsvImportsValidSegments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:autosave_csv_imports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Autosaves any valid segments from records imported from csv files that are complete_shipped or complete_scrapped';
    
    /**
     * The shop finding segments array.
     *
     * @var array
     */
    public $shopFindingSegments = [
        'HDR_Segment' => HDR_Segment::class,
        'AID_Segment' => AID_Segment::class,
        'API_Segment' => API_Segment::class,
        'ATT_Segment' => ATT_Segment::class,
        'EID_Segment' => EID_Segment::class,
        'LNK_Segment' => LNK_Segment::class,
        'RCS_Segment' => RCS_Segment::class,
        'RLS_Segment' => RLS_Segment::class,
        'SAS_Segment' => SAS_Segment::class,
        'SPT_Segment' => SPT_Segment::class,
        'SUS_Segment' => SUS_Segment::class,
    ];
    
    /**
     * The piece part segments array.
     *
     * @var array
     */
    public $piecePartSegments = [
        'WPS_Segment' => WPS_Segment::class,
        'NHS_Segment' => NHS_Segment::class,
        'RPS_Segment' => RPS_Segment::class,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifications = Notification::withTrashed()->with('pieceParts')
            ->whereNotNull('plant_code')
            ->where('is_csv_import', true)
            ->whereNull('csv_import_autosaved_at')
            ->whereIn('status', ['complete_shipped', 'complete_scrapped'])
            ->chunk(100, function($notifications) {
                
                foreach ($notifications as $notification) {
                    
                    foreach ($this->shopFindingSegments as $segmentName => $class) {
                        
                        // If not, validate and save if valid.
                        $profiler = new ValidationProfiler($segmentName, $notification, $notification->get_RCS_SFI());
                        $attributes = $class::getKeys();
                        
                        $data = [];
                        
                        foreach ($attributes as $attribute) {
                            $methodName = $class::__callStatic('getPrefix', []).$attribute;
                            
                            if (method_exists($notification, $methodName)) {
                                $data[$attribute] = $notification->$methodName();
                            }
                        }
                        
                        $validator = Validator::make($data, $profiler->getValidationRules($notification->get_RCS_SFI()));
                
                        // Add any conditional validation.
                        $validatedConditionally = $profiler->conditionalValidation($validator);
                        
                        if (!$validatedConditionally->fails()) {
                            $this->saveShopFindingSegment($segmentName, $data, $notification);
                            //$message = 'Autosaved ' . $segmentName . ' for CSV Imported Notification: ' . $notification->get_RCS_SFI();
                            //$this->info($message);
                            //Log::info($message);
                        } else {
                            $message = 'Could not autosave segment: ' . $segmentName . ' for CSV Imported Notification: ' . $notification->get_RCS_SFI();
                            Log::info($message, $validatedConditionally->errors()->all());
                            //$this->error($message);
                            //mydd($validatedConditionally->errors()->all());
                            //mydd($data);
                            //$this->error("$segmentName ID: {$notification->get_RCS_SFI()} invalid!!!");
                            //$this->error(mydd($data));
                            //$this->error(mydd($validatedConditionally->errors()->all()));
                        }
                        
                        if ($notification->PieceParts) {
                            foreach ($notification->PieceParts as $piecePart) {
                                
                                foreach ($this->piecePartSegments as $segmentName => $class) {
                                    $profiler = new ValidationProfiler($segmentName, $piecePart, $piecePart->notification_id);
                                    $attributes = $class::getKeys();
                                    
                                    $data = [];
                                    
                                    foreach ($attributes as $attribute) {
                                        $methodName = $class::__callStatic('getPrefix', []).$attribute;
                                        
                                        if (method_exists($piecePart, $methodName)) {
                                            $data[$attribute] = $piecePart->$methodName();
                                        }
                                    }
                                    
                                    $validator = Validator::make($data, $profiler->getValidationRules($piecePart->id));
                            
                                    // Add any conditional validation.
                                    $validatedConditionally = $profiler->conditionalValidation($validator);
                                    
                                    if (!$validatedConditionally->fails()) {
                                        $this->savePiecePartSegment($segmentName, $data, $notification, $piecePart->get_WPS_PPI());
                                        //$ppmessage = 'Autosaved Piece Part '.$segmentName.' ID: '.$piecePart->get_WPS_PPI().', for CSV Imported Notification: '.$notification->get_RCS_SFI();
                                        //$this->info($ppmessage);
                                        //Log::info($ppmessage);
                                    } else {
                                        $ppmessage = 'Could not autosave Piece Part '.$segmentName.' ID: '.$piecePart->get_WPS_PPI().', for CSV Imported Notification: '.$notification->get_RCS_SFI();
                                        Log::info($ppmessage, $validatedConditionally->errors()->all());
                                        //$this->error($ppmessage);
                                        //mydd("$segmentName ID: {$piecePart->get_WPS_PPI()} invalid!!!");
                                        //mydd($data);
                                        //$this->error("$segmentName ID: {$piecePart->get_WPS_PPI()} invalid!!!");
                                        //$this->error(mydd($data));
                                        //$this->error(mydd($validatedConditionally->errors()->all()));
                                    }
                                }
                            }
                        }
                    }
                }
            }); // End of chunking.
        
        // Save autosaved date so it won't re-attempt to autosave at the next run.
        Notification::withTrashed()->with('pieceParts')
            ->whereNotNull('plant_code')
            ->where('is_csv_import', true)
            ->whereNull('csv_import_autosaved_at')
            ->whereIn('status', ['complete_shipped', 'complete_scrapped'])
            ->update(['csv_import_autosaved_at' => Carbon::now()]);
    }
    
    /**
     * Save the shop finding segment.
     *
     * @param (string) $segmentName
     * @param (array) $data
     * @param \App\Notification $notification
     * @return void
     */
    private function saveShopFindingSegment(string $segmentName, array $data, Notification $notification)
    {
        $class = $this->shopFindingSegments[$segmentName];
        
        $shopFinding = ShopFinding::withTrashed()->firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        
        if ($segmentName == 'HDR_Segment') {
            $class::createOrUpdateSegment($data, $shopFinding->id, true);
        } else {
            $class::createOrUpdateSegment($data, $shopFindingsDetail->id, true);
        }
    }
    
    /**
     * Save the piece part segment.
     *
     * @param (string) $segmentName
     * @param (array) $data
     * @param \App\Notification $notification
     * @param (string) $piecePartDetailId
     * @return void
     */
    private function savePiecePartSegment(string $segmentName, array $data, Notification $notification, string $piecePartDetailId)
    {
        $class = $this->piecePartSegments[$segmentName];
        
        $shopFinding = ShopFinding::withTrashed()->firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);
        
        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePartDetail = PiecePartDetail::withTrashed()->firstOrCreate(['id' => $piecePartDetailId, 'piece_part_id' => $piecePart->id]);
        
        $class::createOrUpdateSegment($data, $piecePartDetailId, true);
    }
}
