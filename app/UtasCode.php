<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class UtasCode extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Collins/Utas Cage Code.
     * To be substituted on export.
     *
     * @const string
     */
    const CAGE_CODE = '73030';
    
    /**
     * Get all the different UTAS part codes, cache them for 60 minutes if not already cached.
     *
     * @return array
     */
    public static function getAllUtasCodes()
    {



        // LJM MGTSUP-703 there is something broken with the caching or getting this from the database. see notes on the ticket
        $utasParts = self::distinct()->select('MATNR')->get();

        $codes = [];

        if (count($utasParts)) {
            foreach ($utasParts as $utasPart) {
                $codes[] = $utasPart->MATNR;
            }
        }

        return $codes;


        // LJM MGTSUP-703 above is now running instead of the original below.
        //$value = Cache::remember('utasCodes', 3600, function () {
        //    $utasParts = self::distinct()->select('MATNR')->get();

        //    $codes = [];

        //    if (count($utasParts)) {
        //        foreach ($utasParts as $utasPart) {
        //            $codes[] = $utasPart->MATNR;
        //        }
        //    }

        //    return $codes;
        //});

        //return $value;








    }
    
    /**
     * Convert DESCR attribute to lowercase to comply with validation.
     *
     * @param (string) $value
     * @return string
     */
    public function getDescrAttribute($value)
    {
        /* No caps, blanks filled with a ".", spaces allowed, max chars 40 */
        
        $value = strtolower($value);
        
        if (empty($value)) {
            $value = '.';
        }
        
        $value = substr($value, 0, 40);
        
        return $value;
    }
    
    /**
     * Convert FEAT attribute to lowercase to comply with validation.
     *
     * @param (string) $value
     * @return string
     */
    public function getFeatAttribute($value)
    {
        /* Blank lines allowed, spaces allowed, max chars 40 */
        
        $value = strtolower($value);
        $value = substr($value, 0, 40);
        
        return $value;
    }
    
    /**
     * Convert COMP attribute to first letter uppercase to comply with validation.
     *
     * @param (string) $value
     * @return string
     */
    public function getCompAttribute($value)
    {
        /* 1st character cap, no blank lines, spaces allowed, max chars 40 */
        
        $value = ucfirst($value);
        
        if (empty($value)) {
            $value = '.';
        }
        
        $value = substr($value, 0, 40);
        
        return $value;
    }
    
    /**
     * Convert SUP attribute to uppercase to comply with validation.
     *
     * @param (string) $value
     * @return string
     */
    public function getSubAttribute($value)
    {
        /* All caps, no blank lines, spaces allowed, max chars 40 */
        
        $value = strtoupper($value);
        
        if (empty($value)) {
            $value = '.';
        }
        
        $value = substr($value, 0, 40);
        
        return $value;
    }
}
