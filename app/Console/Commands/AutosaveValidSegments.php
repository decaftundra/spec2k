<?php

namespace App\Console\Commands;

use App\HDR_Segment;
use App\Notification;
use App\PieceParts\NHS_Segment;
use App\PieceParts\PiecePart;
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
use App\ValidationProfiler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AutosaveValidSegments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:autosave_valid_segments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Autosaves any valid segments from saved records that are complete_shipped or complete_scrapped';

    /**
     * The shop finding segments array.
     *
     * @var array
     */
    public array $shopFindingSegments = [
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
    public array $piecePartSegments = [
        'WPS_Segment' => WPS_Segment::class,
        'NHS_Segment' => NHS_Segment::class,
        'RPS_Segment' => RPS_Segment::class,
    ];


    public function handle(): void
    {
        Log::info('LJMDEBUG1 MGTSUP-953: START ' . date("Y-m-d H:i:s"));
        /**
         * Retrieves `Shopfinding` records from the database with specific conditions and includes
         * related segments using Laravel's Eloquent ORM.
         *
         * The `with` method is used to eager load relationships, which helps in reducing the number of database
         * queries by loading related data along with the main Shopfinding records.
         * In the first line, it loads the `HDR_Segment` relationship.
         *
         * Next, it retrieves Shopfinding records that meet specific criteria and includes related segments using
         * the `with` method for eager loading.
         * The `whereIn` and `whereNotNull` methods filter the records based on their status and plant code.
         */
        $shopFindings = ShopFinding::with('HDR_Segment')
            ->with('ShopFindingsDetail.RCS_Segment')
            ->with('ShopFindingsDetail.SAS_Segment')
            ->doesntHave('ShopFindingsDetail.SUS_Segment')
            ->with('ShopFindingsDetail.RLS_Segment')
            ->with('ShopFindingsDetail.LNK_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->with('ShopFindingsDetail.EID_Segment')
            ->with('ShopFindingsDetail.API_Segment')
            ->with('ShopFindingsDetail.ATT_Segment')
            ->with('ShopFindingsDetail.SPT_Segment')
            ->whereIn('status', ['complete_shipped', 'complete_scrapped'])
            ->whereNotNull('plant_code')
            ->get();

        $notifications = Notification::with('pieceParts')
            ->whereIn('id', $shopFindings->pluck('id')->toArray())
            ->whereNotNull('plant_code')
            ->whereIn('status', ['complete_shipped', 'complete_scrapped'])
            /**
             * Uses the chunk method to process `Notification` records in smaller batches, which helps manage memory usage
             * and improve performance when dealing with large datasets. The chunk method retrieves records in chunks of 100 and processes each chunk separately.
             * Within the chunk, the code iterates over each `Notification` record. For each notification,
             * it logs the start of processing and then iterates over the `shopFindingSegments` array, which contains segment names and their corresponding class names.
             */
            ->chunk(100, function ($notifications) use ($shopFindings) {

                foreach ($notifications as $notification) {

                    Log::info('LJMDEBUG2 MGTSUP-953: NewNotification ' . date("Y-m-d H:i:s"));

                    foreach ($this->shopFindingSegments as $segmentName => $class) {
                        Log::info('LJMDEBUG3 MGTSUP-953: NewShopFindingSegment ' . date("Y-m-d H:i:s"));

                        // See if there is already a segment saved.
                        if ($segmentName == 'HDR_Segment') {
                            $hasSavedSegment = $shopFindings->find($notification->get_RCS_SFI())->$segmentName;
                        } else {
                            $hasSavedSegment = $shopFindings->find($notification->get_RCS_SFI())->ShopFindingsDetail->$segmentName;
                        }

                        // If not, validate and save if valid.
                        if (!$hasSavedSegment) {
                            $profiler = new ValidationProfiler($segmentName, $notification, $notification->get_RCS_SFI());
                            $attributes = $class::getKeys();
                            $data = [];

                            foreach ($attributes as $attribute) {
                                $methodName = $class::__callStatic('getPrefix', []) . $attribute;

                                if (method_exists($notification, $methodName)) {
                                    $data[$attribute] = $notification->$methodName();
                                }
                            }

                            $validator = Validator::make($data, $profiler->getValidationRules($notification->get_RCS_SFI()));

                            // Add any conditional validation.
                            $validatedConditionally = $profiler->conditionalValidation($validator);

                            if (!$validatedConditionally->fails()) {
                                $this->saveShopFindingSegment($segmentName, $data, $notification);
                            } else {
                                //mydd($validatedConditionally->errors()->all());
                                //mydd($data);
                                //$this->error("$segmentName ID: {$notification->get_RCS_SFI()} invalid!!!");
                                $this->error(mydd($data));
                                $this->error(mydd($validatedConditionally->errors()->all()));
                            }
                        }

                        if ($notification->PieceParts) {

                            foreach ($notification->PieceParts as $piecePart) {
                                Log::info('LJMDEBUG4 MGTSUP-953: NewPiecePart ' . date("Y-m-d H:i:s"));

                                foreach ($this->piecePartSegments as $segmentName => $class) {
                                    Log::info('LJMDEBUG5 MGTSUP-953: NewPiecePartSegment ' . date("Y-m-d H:i:s"));

                                    $piecePartSegmentExists = $class::where('piece_part_detail_id', $piecePart->get_WPS_PPI())->first();

                                    if (!$piecePartSegmentExists) {
                                        $profiler = new ValidationProfiler($segmentName, $piecePart, $piecePart->notification_id);
                                        $attributes = $class::getKeys();

                                        $data = [];

                                        foreach ($attributes as $attribute) {
                                            $methodName = $class::__callStatic('getPrefix', []) . $attribute;

                                            if (method_exists($piecePart, $methodName)) {
                                                $data[$attribute] = $piecePart->$methodName();
                                            }
                                        }

                                        $validator = Validator::make($data, $profiler->getValidationRules($piecePart->id));

                                        // Add any conditional validation.
                                        $validatedConditionally = $profiler->conditionalValidation($validator);

                                        if (!$validatedConditionally->fails()) {
                                            $this->savePiecePartSegment($segmentName, $data, $notification, $piecePart->get_WPS_PPI());
                                        } else {
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
                }
            }); // End of chunking.


        Log::info('LJMDEBUG6 MGTSUP-953: END ' . date("Y-m-d H:i:s"));
    }

    /**
     * Save the shop finding segment.
     *
     * @param (string) $segmentName
     * @param (array) $data
     * @param Notification $notification
     * @return void
     */
    private function saveShopFindingSegment(string $segmentName, array $data, Notification $notification): void
    {
        $class = $this->shopFindingSegments[$segmentName];

        $shopFinding = ShopFinding::firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);

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
     * @param Notification $notification
     * @param (string) $piecePartDetailId
     * @return void
     */
    private function savePiecePartSegment(string $segmentName, array $data, Notification $notification, string $piecePartDetailId) : void
    {
        $class = $this->piecePartSegments[$segmentName];

        $shopFinding = ShopFinding::firstOrCreate(['id' => $notification->get_RCS_SFI()], ['plant_code' => $notification->plant_code]);

        $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePart = PiecePart::firstOrCreate(['shop_finding_id' => $shopFinding->id]);
        $piecePartDetail = PiecePartDetail::firstOrCreate(['id' => $piecePartDetailId, 'piece_part_id' => $piecePart->id]);

        $class::createOrUpdateSegment($data, $piecePartDetailId, true);
    }
}
