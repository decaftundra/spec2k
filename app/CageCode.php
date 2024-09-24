<?php

namespace App;

use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;

class CageCode extends Model
{
    /**
     * The locations that belong to the cage code.
     */
    public function locations()
    {
        return $this->belongsToMany('App\Location');
    }
    
    /**
     * Search locations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search) return $query;
        return $query->where('cage_code', 'LIKE', "%$search%")
            ->orWhere('info', 'LIKE', "%$search%");
    }
    
    /**
     * Get all permitted cage code values.
     *
     * @return array
     */
    public static function getPermittedValues()
    {
        $codesUppercase = array_map('strtoupper', self::pluck('cage_code')->toArray());
        $codesLowercase = array_map('strtolower', $codesUppercase);
        $unknownCodes = ['ZZZZZ', 'zzzzz'];
        
        $codes = array_merge($codesUppercase, $codesLowercase, $unknownCodes);
        
        return $codes;
    }
}