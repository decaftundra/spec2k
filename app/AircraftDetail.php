<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AircraftDetail extends Model
{
    /**
     |-----------------------------
     | Dynamic Local Scopes
     |-----------------------------
     */
    
    public function scopeAircraftFullyQualifiedRegistrationNo($query, $REG = NULL)
    {
        if (!$REG) return $query;
        return $query->where('aircraft_fully_qualified_registration_no', 'LIKE', "$REG%");
    }
    
    public function scopeAircraftIdentificationNo($query, $AIN = NULL)
    {
        if (!$AIN) return $query;
        return $query->where('aircraft_identification_no', 'LIKE', "$AIN%");
    }
    
    public function scopeManufacturerName($query, $MFN = NULL)
    {
        if (!$MFN) return $query;
        return $query->where('manufacturer_name', 'LIKE', "$MFN%");
    }
    
    public function scopeManufacturerCode($query, $MFR = NULL)
    {
        if (!$MFR) return $query;
        return $query->where('manufacturer_code', 'LIKE', "$MFR%");
    }
    
    public function scopeAircraftModelIdentifier($query, $AMC = NULL)
    {
        if (!$AMC) return $query;
        return $query->where('aircraft_model_identifier', 'LIKE', "$AMC%");
    }
    
    public function scopeAircraftSeriesIdentifier($query, $ASE = NULL)
    {
        if (!$ASE) return $query;
        return $query->where('aircraft_series_identifier', 'LIKE', "$ASE%");
    }
    
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search || (strlen($search) <= 2)) return $query;
        
        return $query->where('aircraft_fully_qualified_registration_no', 'LIKE', "%$search%")
            ->orWhere('aircraft_identification_no', 'LIKE', "%$search%")
            ->orWhere('manufacturer_name', 'LIKE', "%$search%")
            ->orWhere('manufacturer_code', 'LIKE', "%$search%")
            ->orWhere('aircraft_model_identifier', 'LIKE', "%$search%")
            ->orWhere('aircraft_series_identifier', 'LIKE', "%$search%");
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $partNo
     * @param (string) $subassemblyName
     * @param (string) $component
     * @param (string) $feature
     * @param (string) $description
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAircraftDetail(
        $REG = NULL,
        $AIN = NULL,
        $MFN = NULL,
        $MFR = NULL,
        $AMC = NULL,
        $ASE = NULL
    )
    {
        return static::distinct()->aircraftFullyQualifiedRegistrationNo($REG)
            ->aircraftIdentificationNo($AIN)
            ->manufacturerName($MFN)
            ->manufacturerCode($MFR)
            ->aircraftModelIdentifier($AMC)
            ->aircraftSeriesIdentifier($ASE)
            ->get();
    }
    
    /**
     * Get Aircraft Fully Qualified Registration Nos.
     *
     * @param (string) $REG
     * @return array
     */
    public static function getAircraftFullyQualifiedRegistrationNo($REG = NULL)
    {
        return static::distinct()
            ->select('aircraft_fully_qualified_registration_no')
            ->where('aircraft_fully_qualified_registration_no', 'LIKE', "$REG%")
            ->orderBy('aircraft_fully_qualified_registration_no', 'asc')
            ->pluck('aircraft_fully_qualified_registration_no')
            ->toArray();
    }
    
    public static function getAircraftIdentificationNo($AIN = NULL)
    {
        return static::distinct()
            ->select('aircraft_identification_no')
            ->where('aircraft_identification_no', 'LIKE', "$AIN%")
            ->orderBy('aircraft_identification_no', 'asc')
            ->pluck('aircraft_identification_no')
            ->toArray();
    }
    
    public static function getManufacturerName($MFN = NULL)
    {
        return static::distinct()
            ->select('manufacturer_name')
            ->where('manufacturer_name', 'LIKE', "$MFN%")
            ->orderBy('manufacturer_name', 'asc')
            ->pluck('manufacturer_name')
            ->toArray();
    }
    
    public static function getManufacturerCode($MFR = NULL)
    {
        return static::distinct()
            ->select('manufacturer_code')
            ->where('manufacturer_code', 'LIKE', "$MFR%")
            ->orderBy('manufacturer_code', 'asc')
            ->pluck('manufacturer_code')
            ->toArray();
    }
    
    public static function getAircraftModelIdentifier($AMC = NULL)
    {
        return static::distinct()
            ->select('aircraft_model_identifier')
            ->where('aircraft_model_identifier', 'LIKE', "$AMC%")
            ->orderBy('aircraft_model_identifier', 'asc')
            ->pluck('aircraft_model_identifier')
            ->toArray();
    }
    
    public static function getAircraftSeriesIdentifier($ASE = NULL)
    {
        return static::distinct()
            ->select('aircraft_series_identifier')
            ->where('aircraft_series_identifier', 'LIKE', "$ASE%")
            ->orderBy('aircraft_series_identifier', 'asc')
            ->pluck('aircraft_series_identifier')
            ->toArray();
    }
    
    /**
     * Get an array of manufacturer codes with comma-separated list of names for dropdown menu.
     *
     * @return array $manufacturerCodesDropDown
     */
    public static function getManufacturerCodesDropDown()
    {
        $manufacturerCodes = DB::table('aircraft_details')
        ->select(
            DB::raw("GROUP_CONCAT(DISTINCT(manufacturer_name) ORDER BY manufacturer_name SEPARATOR ', ') as name"),
            'manufacturer_code AS code'
        )
        ->where('manufacturer_code', '!=', 'ZZZZZ')
        ->OrderBy('manufacturer_code')
        ->groupBy('manufacturer_code')
        ->pluck('name', 'code')
        ->toArray();
        
        asort($manufacturerCodes); // Order by manufacturer name.
        
        $manufacturerCodes['ZZZZZ'] = 'Others'; // Add others.
        
        $manufacturerCodesDropDown = [];
        
        // Add code in parenthesis to name.
        foreach($manufacturerCodes as $code => $name) {
            $manufacturerCodesDropDown[$code] = '(' . $code . ') ' . $name;
        }
        
        return $manufacturerCodesDropDown;
    }
    
    /**
     * Get all the unique manufacturer cage codes.
     *
     * @return array
     */
    public static function getPermittedValues()
    {
        $codesUppercase = array_map('strtoupper', self::distinct()->pluck('manufacturer_code')->toArray());
        $codesLowercase = array_map('strtolower', $codesUppercase);
        $unknownCodes = ['ZZZZZ', 'zzzzz'];
        
        $codes = array_merge($codesUppercase, $codesLowercase, $unknownCodes);
        
        return $codes;
    }
}
