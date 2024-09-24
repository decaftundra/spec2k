<?php

namespace App;

use App\PartList;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use App\Notification;

class Location extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['cage_code'];
    
    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }
    
    /**
     * Get the phone record associated with the user.
     */
    public function part_list()
    {
        return $this->hasOne('App\PartList');
    }
    
    /**
     * The cage codes that belong to the location.
     */
    public function cage_codes()
    {
        return $this->belongsToMany('App\CageCode');
    }
    
    /**
     * If the location is related to one cage code, return it.
     *
     * @return bool
     */
    public function getCageCodeAttribute()
    {
        $this->load('cage_codes');
        
        $cageCodes = $this->cage_codes->pluck('cage_code')->toArray();
        
        return count($cageCodes) == 1 ? $cageCodes[0] : null;
    }
    
    /**
     * Get an array of plant codes and location names for location filter.
     * Pre-filtered by location depending on user abilities.
     *
     * @param (string) $ability
     * @return array
     */
    public static function filter(string $ability = NULL)
    {
        $filter = [];
        
        if (!auth()->check()) return $filter;
        
        if ($ability && Gate::has($ability) && Gate::allows($ability)) {
            $locations = static::orderBy('name')->get();
        } else {
            $locations = static::where('id', auth()->user()->location_id)
                ->orderBy('name')
                ->get();
        }
        
        if (count($locations)) {
            foreach ($locations as $location) {
                $filter[$location->plant_code] = $location->name . ' [' . $location->sap_location_name . ']';
            }
        }
        
        return $filter;
    }
    
    /**
     * Scope a query to only include locations that the user can view.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEditable($query)
    {
        if (Gate::allows('view-all-locations')) {
            return $query;
        }
        
        return $query->where('id', auth()->user()->location_id);
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
        return $query->whereHas('cage_codes', function($q) use ($search) {
            $q->where('cage_code', 'LIKE', "%$search%");
        })
            ->orWhere('name', 'LIKE', "%$search%")
            ->orWhere('sap_location_name', 'LIKE', "%$search%")
            ->orWhere('plant_code', 'LIKE', "%$search%")
            ->orWhere('timezone', 'LIKE', "%$search%");
    }
    
    public function scopeRoc($query, $roc = NULL)
    {
        if (!$roc) return $query;
        
        $query->whereHas('cage_codes', function($q) use ($roc) {
            $q->where('cage_code', 'LIKE', "%$roc%");
        });
        
        //mydd($query->toSql());
        
        return $query;
    }
    
    public function scopeRon($query, $ron = NULL)
    {
        if (!$ron) return $query;
        return $query->where('name', 'LIKE', "%$ron%");
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $roc
     * @param (string) $ron
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getReportingOrganisation($roc = NULL, $ron = NULL)
    {
        return static::distinct()->with('cage_codes')->roc($roc)->ron($ron)->get();
    }
    
    /**
     * Get location names.
     *
     * @param (string) $ron
     * @return array
     */
    public static function getNames($ron = NULL)
    {
        return static::distinct()
            ->select('name')
            ->where('name', 'LIKE', "%$ron%")
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();
    }
    
    /**
     * Get location codes.
     *
     * @param (string) $roc
     * @return array
     */
    public static function getCodes($roc = NULL)
    {
        return CageCode::distinct()
            ->select('cage_code')
            ->where('cage_code', 'LIKE', "%$roc%")
            ->orderBy('cage_code', 'asc')
            ->pluck('cage_code')
            ->toArray();
    }
    
    /**
     * Get the first cage code associated with the location by given plant code.
     *
     * @params $plantCode
     * @return string
     */
    public static function getFirstCageCode($plantCode)
    {
        // If the location doesn't exist return a null value.
        $locationExists = self::where('plant_code', $plantCode)->first();
        
        if (!$locationExists) return NULL;
        
        // If the location exists but there are no associated cage codes return the default value.
        $location = self::whereHas('cage_codes')
        ->with(['cage_codes' => function($query){
            $query->orderBy('cage_code', 'asc');
        }])->where('plant_code', $plantCode)->first();
        
        return $location ? $location->cage_codes[0]->cage_code : 'ZZZZZ';
    }
    
    /**
     * Get the location name by given plant code.
     *
     * @params $plantCode
     * @return string
     */
    public static function getReportingOrganisationName($plantCode)
    {
        $location = self::where('plant_code', $plantCode)->first();
        
        // If the location doesn't exist return a null value.
        return $location ? $location->name : NULL;
    }
}
