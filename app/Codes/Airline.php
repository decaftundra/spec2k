<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $connection = 'mysql';
    
    /**
     * Get the Airline name from given ICAO Code.
     *
     * @param (string) $icaoCode
     * @return string
     */
    public static function getAirlineName($icaoCode)
    {
        $airline = self::select('name')->where('icao_code', $icaoCode)->first();
        
        if ($airline) {
            return $airline->name;
        }
        
        return '';
    }
    
    /**
     * Get an array of all the ICAO Codes.
     *
     * @return array
     */
    public static function getAllIcaoCodes()
    {
        return self::whereNotNull('icao_code')->pluck('icao_code')->toArray();
    }
}
