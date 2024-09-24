<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EngineDetail extends Model
{
    use HasFactory;

    public function scopeManufacturerCode($query, $MFR = NULL)
    {
        if (!$MFR) return $query;
        return $query->where('engine_manufacturer_code', 'LIKE', "$MFR%");
    }

    public function scopeSearch($query, $search = NULL)
    {
        if (!$search || (strlen($search) <= 2)) return $query;

        return $query->where('engine_manufacturer', 'LIKE', "%$search%")
            ->orWhere('engine_manufacturer_code', 'LIKE', "%$search%")
            ->orWhere('engine_type', 'LIKE', "%$search%")
            ->orWhere('engines_series', 'LIKE', "%$search%");
    }

    /**
     * Get an array of manufacturer codes with comma-separated list of names for dropdown menu.
     *
     * @return array $manufacturerCodesDropDown
     */
    public static function getManufacturerCodesDropDown()
    {
        $manufacturerCodes = DB::table('engine_details')
        ->select(
            DB::raw("GROUP_CONCAT(DISTINCT(engine_manufacturer) ORDER BY engine_manufacturer SEPARATOR ', ') as name"),
            'engine_manufacturer_code AS code'
        )
        ->where('engine_manufacturer_code', '!=', 'ZZZZZ')
        ->OrderBy('engine_manufacturer_code')
        ->groupBy('engine_manufacturer_code')
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

}
