<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatSerialNumbers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class NotificationPiecePartTableSeeder extends Seeder
{
    use FormatSerialNumbers;
    
    /**
     * The required files from Azure for the seeds.
     *
     * @var array
     */
    protected $requiredFiles = [
        'SAP_AZRBD_001_S2K_11_11.txt',
        'SAP_AZRBD_001_RESERVATION_TEXTS.txt',
        'SAP_AZRBD_001_PIECE_PARTS.txt' // Not really needed.
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        /*try {
            if (!App::environment(['local', 'testing'])) {
                // Move the files from file storage to blob storage.
                $this->copyFileStorageToBlobStorage();
                
                // Copy files to local storage.
                $this->copyFileStorageToLocalStorage();
            }
        
            $pieceParts = $this->getPiecePartData();
            $piecePartsTexts = $this->getPiecePartTexts();
            
            if (count($piecePartsTexts)) {
                foreach ($piecePartsTexts as $key => $value) {
                    if (isset($pieceParts[$key])) {
                        $pieceParts[$key]['wpsFDE'] = $value;
                    }
                }
            }
            
            if (count($pieceParts)) {
                Schema::disableForeignKeyConstraints();
                DB::table('notification_piece_parts')->truncate();
                Schema::enableForeignKeyConstraints();
                
                $collection = collect($pieceParts);
                
                foreach ($collection->chunk(200) as $chunk) {
                    foreach ($chunk as $k => $v) {
                        if ($k) {
                            $piecePart = new App\NotificationPiecePart;
                            $piecePart->fill($v);
                            $piecePart->id = $k;
                            $piecePart->save();
                        }
                    }
                }
            }
            
            if (!App::environment(['local', 'testing'])) {
                // Delete files from file storage.
                $this->deleteFilesFromFileStorage();
            }
            
            if (!App::environment('testing')) {
                Mail::raw('Real Notifications Piece Parts seeded without errors.', function ($message) {
                    $message->subject('Real Notifications Piece Parts Seed Success')
                        ->from('spec2kapp@interactivedimension.com')
                        ->to('mark@interactivedimension.com');
                });
            }
        } catch (\Exception $e) {
            if (!App::environment('testing')) {
                $text = $e->getMessage() . "\n\r" .$e->getTraceAsString();
                
                Mail::raw($text, function ($message) {
                    $message->subject('Real Notification Piece Parts Seed Error')
                        ->from('spec2kapp@interactivedimension.com')
                        ->to('mark@interactivedimension.com');
                });
            }
        }
        */
    }
    
    /**
     * Get the piece parts data.
     *
     * @return array $pieceParts
     */
    public function getPiecePartData()
    {
        $directory = 'app/' . $this->getLocalDataDirectory() . '/';
        $filename = 'SAP_AZRBD_001_S2K_11_11.txt';
        $fullPath = storage_path($directory . $filename);
        
        $pieceParts = [];
        
        if (($handle = fopen($fullPath, "r")) !== FALSE) {
            
            $length = filesize($fullPath);
            
            while (($data = fgetcsv($handle, $length, "\t")) !== FALSE) {
                if (!empty($data[0])) $pieceParts[$data[1]]['notification_id'] = $data[0]; // NOTIFICATION
                if (!empty($data[0])) $pieceParts[$data[1]]['wpsSFI'] = $data[0]; // string 1/50
                if (!empty($data[1])) $pieceParts[$data[1]]['wpsPPI'] = $data[1]; // @PPI string 1/50
                //$data[2] // @SERIAL_NO
                if (!empty($data[3])) $pieceParts[$data[1]]['wpsPFC'] = $data[3]; // @WPS_PFC string 1/1
                if (!empty($data[4])) $pieceParts[$data[1]]['wpsMFR'] = $data[4]; // @WPS_MFR string 5/5
                if (!empty($data[5])) $pieceParts[$data[1]]['wpsMFN'] = $this->cleverTrim($data[5], 55); // @WPS_MFN string 1/55
                if (!empty($data[6])) $pieceParts[$data[1]]['wpsMPN'] = $this->formatSerialNo($data[6], 32); // @WPS_MPN string 1/32 leading zeros
                if (!empty($data[7])) $pieceParts[$data[1]]['wpsSER'] = $this->formatSerialNo($data[7], 15); // @WPS_SER string 1/15 leading zeros
                if (!empty($data[8])) $pieceParts[$data[1]]['wpsFDE'] = $this->cleverTrim($data[8], 100); // @WPS_FDE string 1/100
                if (!empty($data[9])) $pieceParts[$data[1]]['wpsPNR'] = $this->formatSerialNo($data[9], 15); // @WPS_PNR string 1/15 leading zeros
                if (!empty($data[10])) $pieceParts[$data[1]]['wpsOPN'] = $this->cleverTrim($data[10], 32); // @WPS_OPN string 16/32
                if (!empty($data[11])) $pieceParts[$data[1]]['wpsUSN'] = $this->cleverTrim($data[11], 20); // @WPS_USN string 6/20
                if (!empty($data[12])) $pieceParts[$data[1]]['wpsPDT'] = $this->cleverTrim($data[12], 100); // @WPS_PDT string 1/100
                if (!empty($data[13])) $pieceParts[$data[1]]['wpsGEL'] = $this->cleverTrim($data[13], 30); // @WPS_GEL string 1/30
                
                if (!empty((int) $data[14])) {
                    $pieceParts[$data[1]]['wpsMRD'] = Carbon\Carbon::createFromFormat('Ymd', $data[14]); // date
                }
                
                if (!empty($data[15])) $pieceParts[$data[1]]['wpsASN'] = $this->cleverTrim($data[15], 32); // @WPS_ASN string 1/32
                if (!empty($data[16])) $pieceParts[$data[1]]['wpsUCN'] = $this->cleverTrim($data[16], 15); // @WPS_UCN string 1/15
                if (!empty($data[17])) $pieceParts[$data[1]]['wpsSPL'] = $data[17]; // @WPS_SPL string 5/5
                if (!empty($data[18])) $pieceParts[$data[1]]['wpsUST'] = $this->cleverTrim($data[18], 20); // @WPS_UST string 6/20
                
                if (!empty($data[19])) $pieceParts[$data[1]]['nhsMFR'] = $data[19]; // @NHS_MFR string 5/5
                if (!empty($data[20])) $pieceParts[$data[1]]['nhsMPN'] = $this->formatSerialNo($data[20], 32); // @NHS_MPN string 1/32 leading zeros
                if (!empty($data[21])) $pieceParts[$data[1]]['nhsSER'] = $this->formatSerialNo($data[21], 15); // @NHS_SER string 1/15 leading zeros
                if (!empty($data[22])) $pieceParts[$data[1]]['nhsMFN'] = $this->cleverTrim($data[22], 55); // @NHS_MFN string 1/55
                if (!empty($data[23])) $pieceParts[$data[1]]['nhsPNR'] = $this->formatSerialNo($data[23], 15); // @NHS_PNR string 1/15 leading zeros
                if (!empty($data[24])) $pieceParts[$data[1]]['nhsOPN'] = $this->cleverTrim($data[24], 32); // @NHS_OPN string 16/32
                if (!empty($data[25])) $pieceParts[$data[1]]['nhsUSN'] = $this->cleverTrim($data[25], 20); // @NHS_USN string 6/20
                if (!empty($data[26])) $pieceParts[$data[1]]['nhsPDT'] = $this->cleverTrim($data[26], 100); // @NHS_PDT string 1/100
                if (!empty($data[27])) $pieceParts[$data[1]]['nhsASN'] = $this->cleverTrim($data[27], 32); // @NHS_ASN string 1/32
                if (!empty($data[28])) $pieceParts[$data[1]]['nhsUCN'] = $this->cleverTrim($data[28], 15); // @NHS_UCN string 1/15
                if (!empty($data[29])) $pieceParts[$data[1]]['nhsSPL'] = $data[29]; // @NHS_SPL string 5/5
                if (!empty($data[30])) $pieceParts[$data[1]]['nhsUST'] = $this->cleverTrim($data[30], 20); // @NHS_UST string 6/20
                if (!empty($data[31])) $pieceParts[$data[1]]['nhsNPN'] = $this->cleverTrim($data[31], 32); // @NHS_NPN string 1/32
                
                if (!empty($data[32])) $pieceParts[$data[1]]['rpsMPN'] = $this->formatSerialNo($data[32], 32); // @RPS_MPN string 1/32 leading zeros
                if (!empty($data[33])) $pieceParts[$data[1]]['rpsMFR'] = $data[33]; // @RPS_MFR string 5/5
                if (!empty($data[34])) $pieceParts[$data[1]]['rpsMFN'] = $this->cleverTrim($data[34], 55); // @RPS_MFN string 1/55
                if (!empty($data[35])) $pieceParts[$data[1]]['rpsSER'] = $this->formatSerialNo($data[35], 15); // @RPS_SER string 1/15 leading zeros
                if (!empty($data[36])) $pieceParts[$data[1]]['rpsPNR'] = $this->formatSerialNo($data[36], 15); // @RPS_PNR string 1/15 leading zeros
                if (!empty($data[37])) $pieceParts[$data[1]]['rpsOPN'] = $this->cleverTrim($data[37], 32); // @RPS_OPN string 16/32
                if (!empty($data[38])) $pieceParts[$data[1]]['rpsUSN'] = $this->cleverTrim($data[38], 20); // @RPS_USN string 6/20
                if (!empty($data[39])) $pieceParts[$data[1]]['rpsASN'] = $this->cleverTrim($data[39], 32); // @RPS_ASN string 1/32
                if (!empty($data[40])) $pieceParts[$data[1]]['rpsUCN'] = $this->cleverTrim($data[40], 15); // @RPS_UCN string 1/15
                if (!empty($data[41])) $pieceParts[$data[1]]['rpsSPL'] = $data[41]; // @RPS_SPL string 5/5
                if (!empty($data[42])) $pieceParts[$data[1]]['rpsUST'] = $this->cleverTrim($data[42], 20); // @RPS_UST string 6/20
                if (!empty($data[43])) $pieceParts[$data[1]]['rpsPDT'] = $this->cleverTrim($data[43], 100); // @RPS_PDT string 1/100
            }
            
            fclose($handle);
        }
        
        return $pieceParts;
    }
    
    /**
     * Move files from file storage to blob storage, prefixes files with the date.
     *
     * @return void
     */
    public function copyFileStorageToBlobStorage()
    {
        foreach ($this->requiredFiles as $file) {
            $fileContents = Storage::disk('azure-file-storage')->get($file);
            
            Storage::disk('azure-blob-storage-archive')->put(date('Y-m-d') . '_' . $file, $fileContents);
        }
    }
    
    /**
     * Copy the files from azure file storage to local storage.
     *
     * @param (type) $name
     * @return void
     */
    public function copyFileStorageToLocalStorage()
    {
        foreach ($this->requiredFiles as $file) {
            $fileContents = Storage::disk('azure-file-storage')->get($file);
            Storage::disk('local')->put($this->getLocalDataDirectory() . '/' . $file, $fileContents);
        }
    }
    
    /**
     * Delete files from Azure file storage.
     *
     * @return void
     */
    public function deleteFilesFromFileStorage()
    {
        Storage::disk('azure-file-storage')->delete($this->requiredFiles);
    }
    
    /**
     * Get the piece part reservation texts for wpsFDE.
     *
     * @return array $array
     */
    public function getPiecePartTexts()
    {
        $reservationTextDataFile = 'SAP_AZRBD_001_RESERVATION_TEXTS.txt';
        
        $filePath = storage_path('app/' . $this->getLocalDataDirectory() . '/' . $reservationTextDataFile);
        
        $array = [];
        
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, filesize($filePath), "\t")) !== FALSE) {
                $newLineOrContinuation = $data[4] == '*' ? "\n" : ' ';
                
                $array[$data[1]] = isset($array[$data[1]]) ? $array[$data[1]] . $newLineOrContinuation . $data[5] : $data[5];
            }
            fclose($handle);
        }
        
        return $array;
    }
    
    /**
     * Get the directory containing the notification data.
     * These are huge files so for testing we'll use less data.
     *
     * @return string
     */
    public function getLocalDataDirectory()
    {
        return App::environment('testing') ? 'sap-data-testing' : 'sap-data';
    }
}
