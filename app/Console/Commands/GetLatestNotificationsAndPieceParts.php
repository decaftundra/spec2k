<?php

namespace App\Console\Commands;

use App\AircraftDetail;
use App\Exceptions\RecordCountException;
use App\Exceptions\SAPFeedException;
use App\Extract;
use App\Location;
use App\TFWXSpec2KCommon;
use App\Traits\FormatSerialNumbers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Kfirba\QueryGenerator;


class GetLatestNotificationsAndPieceParts extends Command
{
    use FormatSerialNumbers;

    /**
     * Age of files in seconds before the SAPFeedException is thrown.
     *
     * @const int
     */
    const int TIMEOUT = 9000;

    public int $chunkSize = 100;
    public bool $errors = false;
    public $extractMessage = NULL;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:update_notifications_and_piece_parts';

    // Command file for Azure WebJobs Scheduler (plain text file saved with .cmd extension)
    // php %HOME%\site\artisan spec2kapp:update_notifications_and_piece_parts

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the latest SAP data, update the database, and archive the files.';

    /**
     * An array of required files to seed the database.
     *
     * @var array
     */
    protected array $requiredNotificationFiles = [
        'SAP_AZRBD_001_NOTIFICATION_TEXTS.txt',
        'SAP_AZRBD_001_SERVICE_ORDER_TEXTS.txt',
        'SAP_AZRBD_001_NOTIFICATIONS.txt'
    ];

    /**
     * The required files from Azure for the seeds.
     *
     * @var array
     */
    protected array $requiredPiecePartFiles = [
        'SAP_AZRBD_001_RESERVATION_TEXTS.txt',
        'SAP_AZRBD_001_PIECE_PARTS.txt'
    ];

    /**
     * The required file to count the text records.
     *
     * @var string
     */
    protected string $textRecordCountsFile = 'SAP_AZRBD_001_RECORD_COUNTS.txt';

    /**
     * Text record counts.
     *
     * @var array
     */
    protected array $recordCounts = [
        'notifications' => 0,
        'piece_parts' => 0,
        'notification_texts' => 0,
        'reservation_texts' => 0,
        'service_order_texts' => 0
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // copy files to local storage
        // update database records
        // archive files
        $start = Carbon::now('UTC');
        try {
            if (!\App::environment(['local', 'testing'])) {
                // Move the files from file storage to blob storage.
                $this->copyFilesToBlobStorageArchive();

                // Copy files to local storage.
                $this->copyFileStorageToLocalStorage();
            }
            // Get and set record counts.
            $this->getRecordCounts();

            // Compile the data.
            $data = $this->getData();

            // Notifications.
            $notifications = !empty($data['notifications']) ? $data['notifications'] : [];

            // Overwrite data in database.
            if (count($notifications)) {

                // Remove notifications before a certain date.
                if (\App::environment('live')) {
                    foreach ($notifications as $key => $notification) {
                        if (empty($notification['rcsMRD']) || ((int)$notification['rcsMRD']->startOfDay()->format('Ymd') <= 20190402)) {
                            unset($notifications[$key]);
                        }
                    }
                }
                Schema::disableForeignKeyConstraints();
                $notCollection = collect($notifications);

                foreach ($notCollection->chunk($this->chunkSize) as $chunk) {

                    $chunkedNotifications = [];

                    foreach ($chunk as $key => $value) {
                        if ($key) {

                            $notification = new \App\Notification;
                            $notification->id = $key;
                            // LJMDEBUG MGTSUP-864 the values not saved are:
                            // susSHD, susSER, shipped_at, which sets status wrong
                            // if ($key == '000350583803'){
                            // Log::info("LJMDEBUG notification = " . $key);
                            //}

                            $notification->plant_code = $value['plant_code'] ?? NULL;
                            $notification->rcsSFI = $key;
                            $notification->rcsMRD = $value['rcsMRD'] ?? NULL;
                            $notification->rcsMPN = $value['rcsMPN'] ?? NULL;
                            // LJMDEBUG Log::info('MGTSUP-749 : $notification->rcsMPN ' . $notification->rcsMPN);
                            $notification->rcsMFR = $value['rcsMFR'] ?? NULL;
                            $notification->rcsSER = $value['rcsSER'] ?? NULL;

                            // Fetch cage code and repair station name from locations table.
                            if (isset($value['plant_code'])) {
                                $notification->hdrROC = Location::getFirstCageCode($value['plant_code']);
                                $notification->hdrRON = Location::getReportingOrganisationName($value['plant_code']);
                            } else {
                                $notification->hdrROC = NULL;
                                $notification->hdrRON = NULL;
                            }

                            $notification->susSHD = $value['susSHD'] ?? NULL;
                            $notification->susMPN = $value['susMPN'] ?? NULL;
                            $notification->susSER = $value['susSER'] ?? NULL;
                            $notification->susMFR = $value['susMFR'] ?? NULL;

                            // Add empty values so all insert query values match.
                            $notification->rcsREM = NULL;

                            // MGTSUP-749
                            $notification->rlsRMT = NULL;
                            $notification->aidREG = NULL;
                            $notification->aidAIN = NULL;
                            $notification->aidAMC = NULL;
                            $notification->aidASE = NULL;
                            $notification->aidMFN = NULL;
                            $notification->aidMFR = NULL;
                            $notification->eidAET = NULL;
                            $notification->eidAEM = NULL;
                            $notification->eidEPC = NULL;
                            $notification->eidMFR = NULL;

                            if (isset($value['rcsREM'])) {
                                // MGTSUP-749
                                if (TFWXSpec2KCommon::IsCollins($notification->id)) {
                                    // is notification Collins?
                                    // if this is a UTAS/Collins part then we set the sasREM to this value instead.
                                    // LJMDEBUG
                                    //Log::info('MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' rlsRMT before the set was: ' . $notification->rlsRMT);
                                    //Log::info('MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' rcsREM before the set was: ' . $notification->rcsREM)
                                    $notification->rlsRMT = $value['rcsREM'];
                                    //Log::info('MGTSUP-749 : Setting Collins Notification ' . $notification->id . ' rlsRMT to the rcsREM value = ' . $value['rcsREM']);
                                } else {
                                    $notification->rcsREM = $value['rcsREM'];
                                }
                                // Get array of strings between @@ tags.
                                preg_match_all('/@@(.*?)@@/', $value['rcsREM'], $match);

                                if (!empty($match[1])) {

                                    // Get last occurrence of Aircraft Reg No.
                                    $reg = end($match[1]);

                                    if (!empty($reg)) {
                                        $notification->aidREG = substr($reg, 0, 10);

                                        // See if there is a unique aircraft record in the Database with this reg number.
                                        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

                                        if (count($aircraft) == 1) {
                                            $notification->aidAIN = substr($aircraft[0]->aircraft_identification_no, 0, 10);
                                            $notification->aidAMC = substr($aircraft[0]->aircraft_model_identifier, 0, 20);
                                            $notification->aidASE = substr($aircraft[0]->aircraft_series_identifier, 0, 10);
                                            $notification->aidMFN = substr($aircraft[0]->manufacturer_name, 0, 55);
                                            $notification->aidMFR = substr($aircraft[0]->manufacturer_code, 0, 5);

                                            // Add Engine Information.
                                            $notification->eidAET = substr($aircraft[0]->engine_type, 0, 20);
                                            $notification->eidAEM = substr($aircraft[0]->engines_series, 0, 32);
                                            $notification->eidEPC = substr($aircraft[0]->engine_position_identifier, 0, 25);
                                            $notification->eidMFR = substr($aircraft[0]->engine_manufacturer_code, 0, 5);
                                        }
                                    } else { // The technician may enter an empty value '@@@@' to remove previous incorrect data.
                                        $notification->aidREG = NULL;
                                        $notification->aidAIN = NULL;
                                        $notification->aidAMC = NULL;
                                        $notification->aidASE = NULL;
                                        $notification->aidMFN = NULL;
                                        $notification->aidMFR = NULL;
                                        $notification->eidAET = NULL;
                                        $notification->eidAEM = NULL;
                                        $notification->eidEPC = NULL;
                                        $notification->eidMFR = NULL;
                                    }
                                }
                            }
                            // MGTSUP-749
                            if (TFWXSpec2KCommon::IsCollins($notification->id)) { // we have the value now is it Collins?
                                // if this is a UTAS/Collins part then we set the sasREM to this value instead.

                                Log::info('MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' sasREM before the set was: ' . $notification->sasREM);
                                Log::info('MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' sasINT before the set was: ' . $notification->sasINT);


                                $notification->sasREM = isset($value['sasINT']) ? $value['sasINT'] : NULL;
                                Log::info('MGTSUP-749 : Setting Collins Notification ' . $notification->id . ' sasREM to the sasINT value');
                            } else {
                                $notification->sasINT = isset($value['sasINT']) ? $value['sasINT'] : NULL;
                            }

                            $notification->status = isset($value['status']) ? $value['status'] : 'in_progress';
                            $notification->shipped_at = isset($value['shipped_at']) ? $value['shipped_at'] : NULL;
                            $notification->scrapped_at = isset($value['scrapped_at']) ? $value['scrapped_at'] : NULL;
                            $notification->subcontracted_at = isset($value['subcontracted_at']) ? $value['subcontracted_at'] : NULL;
                            $notification->planner_group = isset($value['planner_group']) ? $value['planner_group'] : NULL;
                            $notification->created_at = Carbon::now();
                            $notification->updated_at = Carbon::now();
                            // LJMDEBUG MGTSUP-864 the values not saved are:
                            // susSHD, susSER, shipped_at, which sets status wrong
                            //if ($key == '000350583803') {
                            //    Log::info("LJMDEBUG susSHD = " . $notification->susSHD);
                            //    Log::info("LJMDEBUG susSER = " . $notification->susSER);
                            //    Log::info("LJMDEBUG shipped_at = " . $notification->shipped_at);
                            //    Log::info("LJMDEBUG status = " . $notification->status);
                            //}
                            $notificationArray = $notification->toArray();
                            unset($notificationArray['is_utas']);

                            $chunkedNotifications[] = $notificationArray;
                        }
                        $excludedColumnsFromUpdate = ['deleted_at', 'created_at'];
                        $queryObject = (new QueryGenerator)->generate('notifications', $chunkedNotifications, $excludedColumnsFromUpdate);
                        DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    }
                }
                Schema::enableForeignKeyConstraints();
            }

            // Piece Parts.
            $pieceParts = !empty($data['piece_parts']) ? $data['piece_parts'] : [];

            if (count($pieceParts)) {
                Schema::disableForeignKeyConstraints();

                $ppCollection = collect($pieceParts);

                foreach ($ppCollection->chunk($this->chunkSize) as $chunk) {

                    $chunkedPieceParts = [];

                    foreach ($chunk as $key => $val) {
                        if ($key && isset($val['wpsPPI']) && isset($val['notification_id'])) {
                            $piecePart = new \App\NotificationPiecePart;
                            $piecePart->id = $val['wpsPPI'];
                            $piecePart->notification_id = $val['notification_id'];
                            $piecePart->wpsSFI = isset($val['wpsSFI']) ? $val['wpsSFI'] : NULL;
                            $piecePart->wpsPPI = isset($val['wpsPPI']) ? $val['wpsPPI'] : NULL;
                            $piecePart->wpsMPN = isset($val['wpsMPN']) ? $val['wpsMPN'] : NULL;
                            $piecePart->rpsMPN = isset($val['rpsMPN']) ? $val['rpsMPN'] : NULL;
                            $piecePart->wpsPDT = isset($val['wpsPDT']) ? $val['wpsPDT'] : NULL;


                            // LJMNov22 MGTSUP-301 - this is setting the 5th piece of data in the line to wpsSER when I am being told it should be rpsSER - see the ticket for full details.
                            // $piecePart->wpsSER = isset($val['wpsSER']) ? $val['wpsSER'] : NULL;
                            // instead
                            $piecePart->rpsSER = isset($val['rpsSER']) ? $val['rpsSER'] : NULL;


                            $piecePart->nhsSER = isset($val['nhsSER']) ? $val['nhsSER'] : NULL;
                            $piecePart->nhsMPN = isset($val['nhsMPN']) ? $val['nhsMPN'] : NULL;
                            $piecePart->nhsPNR = isset($val['nhsPNR']) ? $val['nhsPNR'] : NULL;
                            $piecePart->nhsMFR = isset($val['nhsMFR']) ? $val['nhsMFR'] : NULL;
                            $piecePart->nhsSER = isset($val['nhsSER']) ? $val['nhsSER'] : NULL;
                            $piecePart->wpsFDE = isset($val['wpsFDE']) ? $val['wpsFDE'] : NULL;
                            $piecePart->reversal_id = isset($val['reversal_id']) ? $val['reversal_id'] : NULL;
                            $piecePart->created_at = Carbon::now();
                            $piecePart->updated_at = Carbon::now();
                            $piecePart->deleted_at = NULL;

                            $chunkedPieceParts[] = $piecePart->toArray();
                        }

                        $excludedColumnsFromUpdate = ['deleted_at', 'created_at'];

                        $queryObject = (new QueryGenerator)->generate('notification_piece_parts', $chunkedPieceParts, $excludedColumnsFromUpdate);

                        DB::insert($queryObject->getQuery(), $queryObject->getBindings());
                    }
                }

                Schema::enableForeignKeyConstraints();
            }

            $notIds = [];
            $ppNotIds = [];
            $ppReversalIds = [];

            // This will re-check for any old piece parts not in the piece parts text file that need the NHS fields updating.
            if (isset($notCollection)) {

                $notIds = $notCollection->keys()->toArray();

                Log::info('Notification Count: ' . $notCollection->count());
                //Log::info('Notification IDs:', [$notIds]);

                foreach ($notCollection->chunk($this->chunkSize) as $chunk) {
                    $this->saveNHSFields($chunk->keys()->all());
                }
            }

            // This will re-check for any old piece parts with notification ids not in the notifications text file that need the NHS fields updating.
            if (isset($ppCollection)) {

                $ppNotIds = $ppCollection->pluck('notification_id')->toArray();
                $ppReversalIds = $ppCollection->where('reversal_id', '!=', NULL)->pluck('wpsPPI')->toArray();

                Log::info('Piece Part Count: ' . $ppCollection->count());
                //Log::info('Piece Part IDs:', [$ppNotIds]);

                foreach ($ppCollection->chunk($this->chunkSize) as $chunk) {
                    $this->saveNHSFields($chunk->pluck('wpsSFI')->toArray());
                }
            }

            $shopFindingIds = array_unique(array_filter(array_merge($notIds, $ppNotIds)));

            if (count($shopFindingIds)) {
                //                Log::info('LJMDEBUG ShopFindingIDs Total to sync: ' . count($shopFindingIds));

                // LJMDEBUG
//                foreach ($shopFindingIds as $ljmshopfindingid) {
                //                  Log::info('LJMDEBUG ShopFindingID: ' . $ljmshopfindingid);
                //            }


                // Sync statuses, validation and planner groups of shop findings.
                Artisan::call('spec2kapp:sync_shopfindings', ['shopfindingIds' => $shopFindingIds]);

                // Sync piece part reversals...
                //Log::info('Piece Part reversal IDs:', $ppReversalIds);
                Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $ppReversalIds]);
            }
        } catch (\Exception $e) {
            $text = $e->getMessage() . "\n\r" . $e->getFile() . "\n\r" . $e->getTraceAsString();
            $this->errors = true;
            $this->extractMessage .= $text;

            report($e);
        } finally {
            $end = Carbon::now('UTC');

            $extract = new Extract;
            $extract->started_at = $start;
            $extract->ended_at = $end;
            $extract->time_in_seconds = $start->diffInSeconds($end);
            $extract->errors = $this->errors;
            $extract->message = utf8_encode($this->extractMessage);
            $extract->save();

            $latestExtract = Extract::where('errors', 0)->orderBy('created_at', 'DESC')->first();

            Cache::put('latest_extract', $latestExtract, 3600);
        }
    }

    /**
     * Copy the txt files from azure file storage to local storage.
     *
     * @param (type) $name
     * @return void
     */
    private function copyFileStorageToLocalStorage()
    {
        foreach ($this->requiredNotificationFiles as $file) {

            // Timestamp of last modified date.
            $lastModifiedNF = Storage::disk('azure-blob-storage-latest')->lastModified($file);

            if ((time() - $lastModifiedNF) > self::TIMEOUT) {
                throw new SAPFeedException('Out of date Shop Finding SAP Feed file detected. File: ' . $file . '. Last Modified: ' . date('d-m-Y H:i:s', $lastModifiedNF) . '.');
            }

            $fileContents = Storage::disk('azure-blob-storage-latest')->get($file);
            Storage::disk('local')->put($this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $file, $fileContents);
        }

        foreach ($this->requiredPiecePartFiles as $file) {

            // Timestamp of last modified date.
            $lastModifiedPP = Storage::disk('azure-blob-storage-latest')->lastModified($file);

            if ((time() - $lastModifiedPP) > self::TIMEOUT) {
                throw new SAPFeedException('Out of date Piece Part SAP Feed file detected. File: ' . $file . '. Last Modified: ' . date('d-m-Y H:i:s', $lastModifiedPP) . '.');
            }

            $fileContents = Storage::disk('azure-blob-storage-latest')->get($file);
            Storage::disk('local')->put($this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $file, $fileContents);
        }

        // Timestamp of last modified date.
        $lastModifiedRC = Storage::disk('azure-blob-storage-latest')->lastModified($this->textRecordCountsFile);

        if ((time() - $lastModifiedRC) > self::TIMEOUT) {
            throw new SAPFeedException('Out of date Record Count SAP Feed file detected. File: ' . $this->textRecordCountsFile . '. Last Modified: ' . date('d-m-Y H:i:s', $lastModifiedRC) . '.');
        }

        // Copy record counts to local storage.
        $fileContents = Storage::disk('azure-blob-storage-latest')->get($this->textRecordCountsFile);
        Storage::disk('local')->put($this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $this->textRecordCountsFile, $fileContents);
    }

    /**
     * Move files from file storage to blob storage, prefix files with a date.
     *
     * @return void
     */
    private function copyFilesToBlobStorageArchive()
    {
        $date = date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d') . DIRECTORY_SEPARATOR . date('Y-m-d-His');

        foreach ($this->requiredNotificationFiles as $file) {
            $fileContents = Storage::disk('azure-blob-storage-latest')->get($file);
            Storage::disk('azure-blob-storage-archive')->put($date . '_' . $file, $fileContents);
        }

        foreach ($this->requiredPiecePartFiles as $file) {
            $fileContents = Storage::disk('azure-blob-storage-latest')->get($file);
            Storage::disk('azure-blob-storage-archive')->put($date . '_' . $file, $fileContents);
        }

        // Copy record counts to blob storage.
        $fileContents = Storage::disk('azure-blob-storage-latest')->get($this->textRecordCountsFile);
        Storage::disk('azure-blob-storage-archive')->put($date . '_' . $this->textRecordCountsFile, $fileContents);
    }

    /**
     * Save any missing NHS fields.
     *
     * @param (array) $notificationIds
     * @return void
     */
    private function saveNHSFields($notificationIds = [])
    {
        $idArray = implode("','", $notificationIds);

        $sql = "update notification_piece_parts
                inner join notifications on notification_piece_parts.notification_id = notifications.id
                set notification_piece_parts.nhsMPN = notifications.rcsMPN,
            	notification_piece_parts.nhsPNR = LEFT(notifications.rcsMPN, 15),
            	notification_piece_parts.nhsMFR = notifications.rcsMFR,
            	notification_piece_parts.nhsSER = notifications.rcsSER
            	where notifications.id in ('$idArray')";

        Schema::disableForeignKeyConstraints();
        DB::statement($sql);
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Get the expected record counts for each file.
     *
     * @return void
     */
    private function getRecordCounts()
    {
        $recordCountsPath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $this->textRecordCountsFile);

        if (($handle = fopen($recordCountsPath, "r")) !== FALSE) {

            $length = filesize($recordCountsPath);

            while (($length > 0) && (!feof($handle))) {

                while (($data = fgets($handle, $length)) !== FALSE) {

                    $data = explode("\t", rtrim($data)); // Tab delimited, trim off new line character.

                    if (isset($data[0])) {
                        switch ($data[0]) {
                            case 'SAP_AZRBD_001_NOTIFICATIONS.txt':
                                $this->recordCounts['notifications'] = isset($data[1]) ? (int)$data[1] : 0;
                                break;
                            case 'SAP_AZRBD_001_PIECE_PARTS.txt':
                                $this->recordCounts['piece_parts'] = isset($data[1]) ? (int)$data[1] : 0;
                                break;
                            case 'SAP_AZRBD_001_NOTIFICATION_TEXTS.txt':
                                $this->recordCounts['notification_texts'] = isset($data[1]) ? (int)$data[1] : 0;
                                break;
                            case 'SAP_AZRBD_001_SERVICE_ORDER_TEXTS.txt':
                                $this->recordCounts['service_order_texts'] = isset($data[1]) ? (int)$data[1] : 0;
                                break;
                            case 'SAP_AZRBD_001_RESERVATION_TEXTS.txt':
                                $this->recordCounts['reservation_texts'] = isset($data[1]) ? (int)$data[1] : 0;
                                break;
                        }
                    }
                }
            }

            fclose($handle);
        }
    }

    /**
     * Compile all notification and piece part data from text files.
     *
     * @return array
     */
    private function getData(): array
    {
        $notifications = $this->getNotifications();
        $pieceParts = $this->getPieceParts($notifications);
        $notificationTexts = $this->getNotificationTexts();
        $serviceOrderTexts = $this->getServiceOrderTexts();
        $piecePartsTexts = $this->getPiecePartTexts();

        // Compile notifications texts.
        if (count($notificationTexts)) {
            foreach ($notificationTexts as $key => $val) {
                $notifications[$key]['rcsREM'] = $val;
            }
        }

        // Combine service order text with notification data.
        if (count($serviceOrderTexts)) {
            foreach ($serviceOrderTexts as $key => $val) {
                $notifications[$key]['sasINT'] = $val;
            }
        }

        $combined = array_replace_recursive($pieceParts, $piecePartsTexts);

        return [
            'notifications' => $notifications,
            'piece_parts' => $combined
        ];
    }

    /**
     * Get an array of notifications from the SAP_AZRBD_001_NOTIFICATIONS text file.
     *
     * @return array $notifications
     */
    private function getNotifications()
    {
        $latestNotificationsFilename = 'SAP_AZRBD_001_NOTIFICATIONS.txt';

        $notifications = [];
        $count = 0;

        $notificationsPath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $latestNotificationsFilename);

        // Compile initial notifications data.
        if (($handle = fopen($notificationsPath, "r")) !== FALSE) {

            $length = filesize($notificationsPath);

            while (($length > 0) && (!feof($handle))) {

                // We use fgets instead of fgetcsv as there are some anomalies regarding escaping quotes.
                while (($data = fgets($handle, $length)) !== FALSE) {

                    $data = explode("\t", rtrim($data)); // Tab delimited, trim off new line character.

                    if (!empty(array_filter($data)))
                        $count++;

                    if (isset($data[0]) && !empty(trim($data[0]))) {

                        /*
                        0 NOTIFICATION RCS-SFI string 1/50
                        1 REQ_START_DATE RCS-MRD date
                        2 CUST_MATERIAL RCS-MPN, NHS-MPN string 1/32
                        3 INDUSTRY_DESC RCS-MFR, NHS-MFR string 5/5
                        4 SERVICE_ORDER - n/a
                        5 CO_CODE_TEXT HDR-RON string 1/55
                        6 SERIAL_NO RCS-SER string 1/15
                        7 GI_DATE - shipped (SUS-SHD) OR subcontracted date (if SUBC_STATUS is 'SUBC')
                        8 CAGE_CODE HDR-ROC string 3/5
                        9  SHIPPED_PN - SUS-MPN
                        10 SHIPPED_SN - SUS-SER
                        11 SHIPPED_PN_CAGE_CODE - SUS-MFR
                        12 SCRAP_DATE - Application usage only
                        13 PLANNER_GROUP - Application usage only
                        14 SUBC_STATUS - Application usage only
                        15 Plant code / Planning Plant - Application usage only
                        */

                        /*
                        If SAP_scrap_date is not empty
                            Then
                                S2K_shipped_date = SAP_scrap_date
                        Else
                            If SAP_shipping_date is not empty
                                Then    S2K_shipped_date = SAP_ shipping_date
                            Else        S2K_shipped_date = ‘blank’
                            End if
                        End if
                        */

                        if (isset($data[1]) && !empty((int)$data[1])) { // Only import after 3rd April 2019 in live
                            $notifications[$data[0]]['rcsSFI'] = $data[0];

                            if (!empty($data[15]))
                                $notifications[$data[0]]['plant_code'] = $data[15]; // Plant code.

                            if (!empty($data[0]))
                                $notifications[$data[0]]['rcsSFI'] = $data[0]; // NOTIFICATION RCS-SFI string 1/50
                            if (!empty((int)$data[1]))
                                $notifications[$data[0]]['rcsMRD'] = Carbon::createFromFormat('Ymd', $data[1]); // REQ_START_DATE RCS-MRD date

                            // CUST_MATERIAL -> RCS-MPN, NHS-MPN
                            if (!empty($data[2]))
                                $notifications[$data[0]]['rcsMPN'] = $this->cleverTrim($data[2], 32); // CUST_MATERIAL RCS-MPN, NHS-MPN string 1/32

                            // INDUSTRY_DESC -> RCS-MFR, NHS-MFR.
                            if (!empty($data[3]))
                                $notifications[$data[0]]['rcsMFR'] = $this->cleverTrim($data[3], 5); // INDUSTRY_DESC RCS-MFR and NHS-MFR string 5/5

                            if (!empty($data[5]))
                                $notifications[$data[0]]['hdrRON'] = $this->cleverTrim($data[5], 55); // CO_CODE_TEXT HDR-RON string 1/55
                            if (!empty($data[6]))
                                $notifications[$data[0]]['rcsSER'] = $this->cleverTrim($data[6], 15); // SERIAL_NO RCS-SER string 1/15
                            if (!empty($data[8]))
                                $notifications[$data[0]]['hdrROC'] = $this->cleverTrim($data[8], 5); // CAGE_CODE HDR-ROC string 3/5

                            $scrappedDate = !empty($data[12]) ? (int)$data[12] : NULL;

                            $shippedDate = !empty($data[7]) ? (int)$data[7] : NULL;
                            $plannerGroup = !empty($data[13]) ? $data[13] : NULL;
                            $subcontracted = !empty($data[14]) ? $data[14] : NULL;

                            if ($plannerGroup)
                                $notifications[$data[0]]['planner_group'] = $plannerGroup;

                            // Scrap date
                            if (!empty((int)$scrappedDate)) {
                                $notifications[$data[0]]['scrapped_at'] = Carbon::createFromFormat('Ymd', $scrappedDate);
                            }

                            // If Shipping date exists but notification is not subcontracted it is complete.
                            if (empty($subcontracted) && !empty((int)$shippedDate)) {
                                $notifications[$data[0]]['susSHD'] = Carbon::createFromFormat('Ymd', $shippedDate);
                                $notifications[$data[0]]['shipped_at'] = Carbon::createFromFormat('Ymd', $shippedDate);
                                $notifications[$data[0]]['status'] = 'complete_shipped';
                            }

                            // If subcontracted.
                            if (!empty($subcontracted)) {

                                // If Shipping date exists add it to the subcontracted_at column, otherwise add current date.
                                $shippedDate = $shippedDate ?: date('Ymd');

                                $notifications[$data[0]]['subcontracted_at'] = Carbon::createFromFormat('Ymd', $shippedDate);
                                $notifications[$data[0]]['status'] = 'subcontracted';
                            }

                            if (empty((int)$shippedDate) && !empty((int)$scrappedDate)) {
                                $notifications[$data[0]]['status'] = 'complete_scrapped';
                            }

                            if (!empty($data[9]))
                                $notifications[$data[0]]['susMPN'] = $this->cleverTrim($data[9], 32);
                            if (!empty($data[10]))
                                $notifications[$data[0]]['susSER'] = $this->cleverTrim($data[10], 15);
                            if (!empty($data[11]))
                                $notifications[$data[0]]['susMFR'] = $this->cleverTrim($data[11], 5);
                        }
                    }
                }
            }

            fclose($handle);
        }

        try {
            // Throw RecordCountException exception if counts don't smatch.
            if ($count != $this->recordCounts['notifications']) {
                Log::info('Notification array count: ' . $count);
                Log::info('Expected notifications count: ' . $this->recordCounts['notifications']);
                Log::info($notifications);

                $message = 'Notifications count mismatch! Expected: ' . $this->recordCounts['notifications'] . '. but counted: ' . $count;
                $this->errors = true;
                $this->extractMessage .= $message;
                throw new RecordCountException($message);
            }
        } catch (RecordCountException $e) {
            report($e);
        } finally {
            return $notifications;
        }
    }

    /**
     * Get an array of piece parts from SAP_AZRBD_001_PIECE_PARTS text file.
     *
     * @param array $notifications
     * @return array $pieceParts
     */
    private function getPieceParts($notifications = [])
    {
        $latestPiecePartsFilename = 'SAP_AZRBD_001_PIECE_PARTS.txt';

        $pieceParts = [];
        $count = 0;

        $piecePartsPath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $latestPiecePartsFilename);

        // Compile initial piece parts data
        if (($handle = fopen($piecePartsPath, "r")) !== FALSE) {

            $length = filesize($piecePartsPath);

            while (($length > 0) && (!feof($handle))) {

                // We use fgets instead of fgetcsv as there are some anomalies regarding escaping quotes.
                while (($data = fgets($handle, $length)) !== FALSE) {

                    $data = explode("\t", rtrim($data)); // Tab delimited.

                    if (!empty(array_filter($data)))
                        $count++;

                    if (isset($data[0]) && !empty(trim($data[0]))) {

                        /*
                        0 NOTIFICATION - WPS-SFI, notification_id
                        1 SERVICE_ORDER - Not required
                        2 PPI - WPS-PPI
                        3 MATERIAL - WPS-MPN, RPS-MPN
                        4 MATERIAL_DESC - WPS-PDT
                        5 SERIAL_NO - WPS-SER
                        6 QUANTITY - Not required
                        7 UOM - Not required
                        8 RESERVATION - Not required
                        9 RESERVATION_ITEM - Not required
                        10 BOM_ITEM_TEXT WPS-FDE (if no reservation text)
                        11 REVERSAL notated by an 'X'
                        12 REVERSAL id
                        */

                        if (!empty($data[0]))
                            $pieceParts[$data[2]]['notification_id'] = $data[0]; // NOTIFICATION
                        if (!empty($data[0]))
                            $pieceParts[$data[2]]['wpsSFI'] = $data[0]; // string 1/50
                        if (!empty($data[2]))
                            $pieceParts[$data[2]]['wpsPPI'] = $data[2]; // PPI string 1/50
                        if (!empty($data[3]))
                            $pieceParts[$data[2]]['wpsMPN'] = $this->formatSerialNo($data[3], 32); // WPS-MPN, RPS-MPN string 1/32
                        if (!empty($data[3]))
                            $pieceParts[$data[2]]['rpsMPN'] = $this->formatSerialNo($data[3], 32); // WPS-MPN, RPS-MPN string 1/32
                        if (!empty($data[4]))
                            $pieceParts[$data[2]]['wpsPDT'] = $this->cleverTrim($data[4], 100); // WPS-PDT string 1/100

                        // This shouldn't be mapped to both.


                        // LJMNov22 MGTSUP-301 - this is setting the 5th piece of data in the line to wpsSER when I am being told it should be rpsSER - see the ticket for full details.
                        //if (!empty($data[5])) $pieceParts[$data[2]]['wpsSER'] = $this->formatSerialNo($data[5], 15); // WPS-SER, NHS-SER string 1/15
                        // instead
                        if (!empty($data[5]))
                            $pieceParts[$data[2]]['rpsSER'] = $this->formatSerialNo($data[5], 15); // RPS-SER, NHS-SER string 1/15


                        //if (!empty($data[5])) $pieceParts[$data[2]]['nhsSER'] = $this->formatSerialNo($data[5], 15); // WPS-SER, NHS-SER string 1/15

                        // We decided not to map this field.
                        // This will be overwritten if there is text in the piece parts text file.
                        // if (!empty($data[10])) $pieceParts[$data[2]]['wpsFDE'] = $this->cleverTrim($data[10], 100); // WPS-FDE string 1/100

                        // Reversals. If there is an 'X' denoting a reversal AND and corresponding reversal id.
                        if (!empty($data[11]) && !empty($data[12]))
                            $pieceParts[$data[2]]['reversal_id'] = $data[12]; // piece part ID of part to be reversed.

                        if (isset($notifications[$data[0]]['rcsMPN'])) {
                            $pieceParts[$data[2]]['nhsMPN'] = $notifications[$data[0]]['rcsMPN']; // string 1/32
                            $pieceParts[$data[2]]['nhsPNR'] = $this->formatSerialNo($notifications[$data[0]]['rcsMPN'], 15); // string 1/15
                        }

                        if (isset($notifications[$data[0]]['rcsMFR'])) {
                            $pieceParts[$data[2]]['nhsMFR'] = $notifications[$data[0]]['rcsMFR']; // string 5/5
                        }

                        if (isset($notifications[$data[0]]['rcsSER'])) {
                            $pieceParts[$data[2]]['nhsSER'] = $notifications[$data[0]]['rcsSER']; // string 1/15
                        }
                    }
                }
            }

            fclose($handle);
        }

        try {
            // Throw RecordCountException exception if counts don't smatch.
            if ($count != $this->recordCounts['piece_parts']) {
                Log::info('Piece Parts array count: ' . $count);
                Log::info('Expected piece parts count: ' . $this->recordCounts['piece_parts']);
                Log::info($pieceParts);

                $message = 'Piece Parts count mismatch! Expected: ' . $this->recordCounts['piece_parts'] . '. but counted: ' . $count;
                $this->errors = true;
                $this->extractMessage .= $message;
                throw new RecordCountException($message);
            }
        } catch (RecordCountException $e) {
            report($e);
        } finally {
            return $pieceParts;
        }
    }

    /**
     * Get an array of notification texts for rcsREM.
     *
     * @return array $array
     */
    private function getNotificationTexts()
    {
        $notificationTextDataFile = 'SAP_AZRBD_001_NOTIFICATION_TEXTS.txt';

        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $notificationTextDataFile);

        $array = [];
        $count = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {

            $length = filesize($filePath);

            while (($length > 0) && (!feof($handle))) {

                // We use fgets instead of fgetcsv as there are some anomalies regarding escaping quotes.
                while (($data = fgets($handle, $length)) !== FALSE) {

                    $data = explode("\t", rtrim($data)); // Tab delimited.

                    if (!empty(array_filter($data)))
                        $count++;

                    if (isset($data[0]) && !empty(trim($data[0])) && isset($data[1]) && isset($data[2])) {

                        $data = array_map("utf8_encode", $data);

                        $newLineOrContinuation = $data[1] == '*' ? "\n" : ' ';
                        $array[$data[0]] = isset($array[$data[0]]) ? $array[$data[0]] . $newLineOrContinuation . $data[2] : $data[2];
                    }
                }
            }

            fclose($handle);
        }

        try {
            // Throw RecordCountException exception if counts don't smatch.
            if ($count != $this->recordCounts['notification_texts']) {
                Log::info('Notification Texts counter: ' . $count);
                Log::info('Notification Texts count: ' . $this->recordCounts['notification_texts']);
                Log::info($array);

                $message = 'Notifications Texts count mismatch! Expected: ' . $this->recordCounts['notification_texts'] . '. but counted: ' . $count;
                $this->errors = true;
                $this->extractMessage .= $message;
                throw new RecordCountException($message);
            }
        } catch (RecordCountException $e) {
            report($e);
        } finally {
            return $array;
        }
    }

    /**
     * Get an array of service order texts for sasINT.
     *
     * @return array $array
     */
    private function getServiceOrderTexts()
    {
        $serviceOrderTextDataFile = 'SAP_AZRBD_001_SERVICE_ORDER_TEXTS.txt';

        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $serviceOrderTextDataFile);

        $array = [];
        $count = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {

            $length = filesize($filePath);

            while (($length > 0) && (!feof($handle))) {

                // We use fgets instead of fgetcsv as there are some anomalies regarding escaping quotes.
                while (($data = fgets($handle, $length)) !== FALSE) {

                    $data = explode("\t", rtrim($data)); // Tab delimited.

                    if (!empty(array_filter($data)))
                        $count++;

                    if (isset($data[0]) && !empty(trim($data[0])) && isset($data[2]) && isset($data[3])) {

                        $data = array_map("utf8_encode", $data);

                        $newLineOrContinuation = $data[2] == '*' ? "\n" : ' ';
                        $array[$data[0]] = isset($array[$data[0]]) ? $array[$data[0]] . $newLineOrContinuation . $data[3] : $data[3];
                    }
                }
            }

            fclose($handle);
        }

        try {
            // Throw RecordCountException exception if counts don't smatch.
            if ($count != $this->recordCounts['service_order_texts']) {
                Log::info('Service Order Texts counter: ' . $count);
                Log::info('Expected Service Order Texts count: ' . $this->recordCounts['service_order_texts']);
                Log::info($array);

                $message = 'Service Order Texts count mismatch! Expected: ' . $this->recordCounts['service_order_texts'] . '. but counted: ' . $count;
                $this->errors = true;
                $this->extractMessage .= $message;
                throw new RecordCountException($message);
            }
        } catch (RecordCountException $e) {
            report($e);
        } finally {
            return $array;
        }
    }

    /**
     * Get the piece part reservation texts for wpsFDE.
     *
     * @return array $array
     */
    private function getPiecePartTexts()
    {
        $reservationTextDataFile = 'SAP_AZRBD_001_RESERVATION_TEXTS.txt';

        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getLocalDataDirectory() . DIRECTORY_SEPARATOR . $reservationTextDataFile);

        $array = [];
        $count = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {

            $length = filesize($filePath);

            while (($length > 0) && (!feof($handle))) {

                // We use fgets instead of fgetcsv as there are some anomalies regarding escaping quotes.
                while (($data = fgets($handle, $length)) !== FALSE) {
                    $data = explode("\t", rtrim($data)); // Tab delimited.

                    if (!empty(array_filter($data)))
                        $count++;

                    if (isset($data[0]) && !empty(trim($data[0])) && isset($data[1]) && isset($data[4]) && isset($data[5])) {

                        $data = array_map("utf8_encode", $data);

                        $newLineOrContinuation = $data[4] == '*' ? "\n" : ' ';

                        $array[$data[1]]['notification_id'] = $data[0];
                        $array[$data[1]]['wpsSFI'] = $data[0];
                        $array[$data[1]]['wpsPPI'] = $data[1];

                        if (isset($array[$data[1]]) && isset($array[$data[1]]['wpsFDE'])) {
                            $array[$data[1]]['wpsFDE'] = $array[$data[1]]['wpsFDE'] . $newLineOrContinuation . $data[5];
                        } else {
                            $array[$data[1]]['wpsFDE'] = $data[5];
                        }
                    }
                }
            }

            fclose($handle);
        }

        try {
            // Throw RecordCountException exception if counts don't smatch.
            if ($count != $this->recordCounts['reservation_texts']) {
                Log::info('Reservation Texts counter: ' . $count);
                Log::info('Expected Reservation Texts count: ' . $this->recordCounts['reservation_texts']);
                Log::info($array);

                $message = 'Reservation Texts count mismatch! Expected: ' . $this->recordCounts['reservation_texts'] . '. but counted: ' . $count;
                $this->errors = true;
                $this->extractMessage .= $message;
                throw new RecordCountException($message);
            }
        } catch (RecordCountException $e) {
            report($e);
        } finally {
            return $array;
        }
    }

    /**
     * Get the directory containing the notification data.
     * These are huge files so for testing we'll use less data.
     *
     * @return string
     */
    private function getLocalDataDirectory()
    {
        return \App::environment('testing') ? 'sap-data-testing' : 'sap-data';
    }


    /**
     * LJMMay24 MGTSUP-749
     * are we dealing with a Collins Notification
     * however it looks like it can match agains both
     * notifications->rcsMPN
     * and
     * rcs_segments->MPN
     */
    private function IsCollins($NotificationID)
    {

        //        Log::info('MGTSUP-749 : IsCollins '. $NotificationID);
        $iNumberOfCollinsMatches = 0; // this says if we have any collins records at all?

        $Notification_MATNR = "DONTMATCHTHIS";
        $RCS_Segment_MATNR = "DONTMATCHTHIS";


        // NotificationValues
//        $LJMSqlQueryNotifications = " SELECT * FROM notifications WHERE id = " . $NotificationID . " AND rcsMPN IS NOT NULL;";
        $LJMSqlQueryNotifications = " SELECT * FROM notifications WHERE id = " . $NotificationID . " ;";
        //        Log::info('MGTSUP-749 : $LJMSqlQueryNotifications '. $LJMSqlQueryNotifications);
        $recNotifications = DB::select($LJMSqlQueryNotifications);
        foreach ($recNotifications as $recNotification) {
            //            Log::info('MGTSUP-749 : $recNotification->rcsMPN '. $recNotification->rcsMPN);
            $Notification_MATNR = $recNotification->rcsMPN; // we have an MPN
        }

        // is there a shop_finding_details record?
        $shop_finding_details_id = 0;
        $LJMSqlQueryShop_Finding_Details = " SELECT * FROM shop_findings_details WHERE shop_finding_id = " . $NotificationID . ";";
        $recShop_Finding_Details = DB::select($LJMSqlQueryShop_Finding_Details);
        foreach ($recShop_Finding_Details as $recShop_Finding_Detail) {
            $shop_finding_details_id = $recShop_Finding_Detail->id;
            // is there an rcs_segments record
            $LJMSqlQueryRCS_Segments = " SELECT * FROM rcs_segments WHERE shop_findings_detail_id = " . $shop_finding_details_id . " AND MPN IS NOT NULL;";
            $recRCS_Segments = DB::select($LJMSqlQueryRCS_Segments);
            foreach ($recRCS_Segments as $recRCS_Segment) {
                $RCS_Segment_MATNR = $recRCS_Segment->MPN;

                // LJMDEBUG                Log::info('MGTSUP-749 : $RCS_Segment_MATNR ' . $RCS_Segment_MATNR);

            }
        }

        // Notifications Table
        $LJMSqlQueryNotifications = " SELECT count(MATNR) AS NumberOfCollinsMatches FROM utas_codes WHERE MATNR = '" . $Notification_MATNR . "' OR MATNR = '" . $RCS_Segment_MATNR . "' ;";
        //        Log::info('MGTSUP-749 : $LJMSqlQueryNotifications '. $LJMSqlQueryNotifications);
        $NumberOfCollinsMatches = DB::select($LJMSqlQueryNotifications);
        foreach ($NumberOfCollinsMatches as $NumberOfCollinsMatch) {
            $iNumberOfCollinsMatches = $iNumberOfCollinsMatches + $NumberOfCollinsMatch->NumberOfCollinsMatches;
        }


        if ($iNumberOfCollinsMatches > 0) {
            Log::info('MGTSUP-974 : Setting Collins Notification ' . $NotificationID . ' Query was: ' . $LJMSqlQueryNotifications);


            return true;
        } else {
            return false;
        }
    }


}

