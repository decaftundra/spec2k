<?php

namespace App;

use Carbon\Carbon;
use App\AircraftDetail;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Storage;

class BoeingData extends AircraftDetail
{
    /**
     * The csv file base name.
     *
     * @var string
     */
    protected $fileBaseName = 'boeing-data-';
    
    /**
     * The boeing csv directory.
     *
     * @var string
     */
    protected $csvDirectory = 'boeing-csv-files';
    
    /**
     * The aircraft details mysql table dump directory.
     *
     * @var string
     */
    protected $dumpDirectory = 'aircraft-detail-dumps';
    
    /**
     * The aircraft details sql file base name.
     *
     * @var string
     */
    protected $sqlFilename = 'aircraft-details-';
    
    /**
     * The aircraft details database table name.
     *
     * @var string
     */
    protected $tableName = 'aircraft_details';
    
    /**
     * The current timestamp.
     *
     * @var integer
     */
    protected $timestamp;
    
    /**
     * The database username.
     *
     * @var string
     */
     private $userName;
     
     /**
      * The database password.
      *
      * @var string
      */
    private $password;
    
    /**
     * The database name.
     *
     * @var string
     */
    private $databaseName;
    
    /**
     * The Boeing Data validation rules.
     *
     * @var array
     */
    public static $rules = ['file' => 'required|file|mimes:csv,txt'];
    
    /**
     * Set the database credentials and current timestamp.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->userName = config('database.connections.mysql.username');
        $this->password = config('database.connections.mysql.password');
        $this->databaseName = config('database.connections.mysql.database');
        $this->timestamp = Carbon::now()->timestamp;
    }
    
    /**
     * Get the csv directory.
     *
     * @return string
     */
    public function getCsvDirectory()
    {
        return $this->csvDirectory;
    }
    
    /**
     * Get the csv filename.
     *
     * @var string
     */
    public function getFilename()
    {
        // Create unique filename.
        return $this->fileBaseName . $this->timestamp . '.csv';
    }
    
    /**
     * Dump the aircraft details into an sql file and save to relevant directory.
     *
     * @return boolean
     */
    public function dumpSql()
    {
        // Create dump directory if it doesn't already exist.
        if (!Storage::disk('local')->exists($this->dumpDirectory)) {
            Storage::disk('local')->makeDirectory($this->dumpDirectory);
        }
        
        $sqlFilePath = storage_path('app' . DIRECTORY_SEPARATOR . $this->dumpDirectory) . DIRECTORY_SEPARATOR . $this->sqlFilename . $this->timestamp . '.sql';
        
        // Backup current aircraft details table.
        return MySql::create()
            ->setDbName($this->databaseName)
            ->setUserName($this->userName)
            ->setPassword($this->password)
            ->includeTables([$this->tableName])
            ->doNotCreateTables()
            ->dumpToFile($sqlFilePath);
    }
    
    /**
     * Get the data from the csv file, format and convert to an array.
     *
     * @return mixed false|array
     */
    public function getData()
    {
        /*
        Expected csv columns.
        
        0 => id (no name)
        1 => BUS_CD
        2 => ICAO_BUS_CD
        3 => BUS_NM
        4 => BUS_SHRT_NM
        5 => AC_ID_NO = Aircraft Identification No???
        6 => AC_REG_NO = Aircraft Fully Qualified Registration No???
        7 => AC_OPR_INTL_ID
        8 => AC_VAR_BLK_NO
        9 => AC_LINE_NO
        10 => AFR_MFR_CD = Manufacturer Code???
        11 => AC_MDL_ID = Aircraft Model Identifier???
        12 => AC_SER_ID = Aircraft Series Identifier???
        13 => USG_TYPE_CD
        14 => AC_DLVRY_DT
        15 => AC_RMV_SERV_CD
        16 => AC_RMV_SERV_DS
        */
        
        $boeingData = [];
        
        $filepath = storage_path('app' . DIRECTORY_SEPARATOR . $this->getCsvDirectory()) . DIRECTORY_SEPARATOR . $this->getFilename();
            
        // Compile initial notifications data.
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            
            $i = 0;
            $length = filesize($filepath);
            $error = 0;
            
            if ($length == 0) {
                fclose($handle);
                return false;
            }
            
            while (($data = fgetcsv($handle, $length, ",")) !== FALSE) {
                // Check column names are as expected.
                if (!empty($data) && $i == 0) {
                    if (!isset($data[1]) || empty(trim($data[1])) || ($data[1] != 'BUS_CD')) $error = 1;
                    if (!isset($data[2]) || empty(trim($data[2])) || ($data[2] != 'ICAO_BUS_CD')) $error = 1;
                    if (!isset($data[3]) || empty(trim($data[3])) || ($data[3] != 'BUS_NM')) $error = 1;
                    if (!isset($data[4]) || empty(trim($data[4])) || ($data[4] != 'BUS_SHRT_NM')) $error = 1;
                    if (!isset($data[5]) || empty(trim($data[5])) || ($data[5] != 'AC_ID_NO')) $error = 1;
                    if (!isset($data[6]) || empty(trim($data[6])) || ($data[6] != 'AC_REG_NO')) $error = 1;
                    if (!isset($data[7]) || empty(trim($data[7])) || ($data[7] != 'AC_OPR_INTL_ID')) $error = 1;
                    if (!isset($data[8]) || empty(trim($data[8])) || ($data[8] != 'AC_VAR_BLK_NO')) $error = 1;
                    if (!isset($data[9]) || empty(trim($data[9])) || ($data[9] != 'AC_LINE_NO')) $error = 1;
                    if (!isset($data[10]) || empty(trim($data[10])) || ($data[10] != 'AFR_MFR_CD')) $error = 1;
                    if (!isset($data[11]) || empty(trim($data[11])) || ($data[11] != 'AC_MDL_ID')) $error = 1;
                    if (!isset($data[12]) || empty(trim($data[12])) || ($data[12] != 'AC_SER_ID')) $error = 1;
                    if (!isset($data[13]) || empty(trim($data[13])) || ($data[13] != 'USG_TYPE_CD')) $error = 1;
                    if (!isset($data[14]) || empty(trim($data[14])) || ($data[14] != 'AC_DLVRY_DT')) $error = 1;
                    if (!isset($data[15]) || empty(trim($data[15])) || ($data[15] != 'AC_RMV_SERV_CD')) $error = 1; // Aircraft removed from service code.
                    if (!isset($data[16]) || empty(trim($data[16])) || ($data[16] != 'AC_RMV_SERV_DS')) $error = 1;
                }
                
                if ($error) {
                    fclose($handle);
                    return false;
                }
                
                if (!empty($data) && $i >= 1) {
                    $boeingData[$i]['aircraft_identification_no'] = !empty(trim($data[5])) ? (string) trim($data[5]) : NULL; // This is always a unique value.
                    $boeingData[$i]['aircraft_fully_qualified_registration_no'] = !empty(trim($data[6])) ? (string) trim($data[6]) : NULL;
                    $boeingData[$i]['manufacturer_name'] = 'Boeing';
                    $boeingData[$i]['manufacturer_code'] = !empty(trim($data[10])) ? (string) trim($data[10]) : NULL;
                    $boeingData[$i]['aircraft_model_identifier'] = !empty(trim($data[11])) ? (string) trim($data[11]) : NULL;
                    $boeingData[$i]['aircraft_series_identifier'] = !empty(trim($data[12])) ? (string) trim($data[12]) : NULL;
                    $boeingData[$i]['removed_from_service'] = !empty(trim($data[15])) ? true : false; // These aircraft will be removed from current database.
                    $boeingData[$i]['created_at'] = Carbon::now()->format('Y-m-d- H:i:s');
                    $boeingData[$i]['updated_at'] = Carbon::now()->format('Y-m-d- H:i:s');
                }
                $i++;
            }
            
            fclose($handle);
            return $boeingData;
        }
    }
}
