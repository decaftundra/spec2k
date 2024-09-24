<?php

namespace App;

use App\Events\PartListSaved;
use App\Events\PartListDeleted;
use Illuminate\Validation\Rule;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use App\Traits\RecordActivityTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\RecordableInterface;

class PartList extends Model implements RecordableInterface
{
    use RecordActivityTrait;
    
    /**
     * The part list context array.
     *
     * @var array
     */
    public static $contexts = [
        //'include',
        'exclude'
    ];
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => PartListSaved::class,
        'deleted' => PartListDeleted::class,
    ];
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('part-list.edit', $this->id);
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View ' . $this->location->name . ' Part List';
    }
    
    /**
     * Get the location that owns the part list.
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }
    
    /**
     * Scope a query to only include locations that the user can view.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEditable($query)
    {
        if (Gate::allows('view-all-part-lists')) {
            return $query;
        }
        
        return $query->where('location_id', auth()->user()->location->id);
    }
    
    /**
     * Get the part list validation rules.
     *
     * @param (integer) $id
     * @return array
     */
    public static function getRules($id = NULL)
    {
        $rules = [
            'location_id' => 'required|integer',
            //'context' => ['required', Rule::in(self::$contexts)],
            'parts' => 'required'
        ];
        
        if ($id) {
            unset($rules['location_id']);
        }
        
        return $rules;
    }
    
    /**
     * Get an array of part numbers.
     *
     * @return array
     */
    public function getParts()
    {
        // Converts to array, trims whitespace and removes null and empty values.
        return preg_split('@(?:\s*,\s*|^\s*|\s*$)@', $this->parts, NULL, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Get an array of all excluded Notification ids.
     *
     * @return array
     */
    public function getExcludedNotificationIds()
    {
        $allShopFindingIds = ShopFinding::withTrashed()->pluck('id')->toArray();
        
        $wildcards = [];
        $array = [];
        
        array_map(function($value) use (&$wildcards, &$array) {
            if (stripos($value, '*') === false) {
                $array[] = $value;
            } else {
                $wildcards[] = str_ireplace('*', '%', $value);
            }
        }, $this->getParts());
        
        $inSql = implode("','", $array);
        
        $inSql = "'" . $inSql . "'";
        
        $likeSql = '';
        
        if (count($wildcards)) {
            foreach ($wildcards as $wildcard) {
                $likeSql .= ' OR rcsMPN LIKE "' . $wildcard . '"';
            }
        }
        
        $sql = "(rcsMPN in ($inSql) $likeSql) and (deleted_at is NULL and plant_code = '{$this->location->plant_code}')";
        
        return DB::table('notifications')
                 ->select('*')
                 ->whereNotIn('notifications.id', $allShopFindingIds)
                 ->whereRaw($sql)
                 ->pluck('id')
                 ->toArray();
    }
    
    /**
     * Get ids of all exluded Shop Findings.
     *
     * @return array
     */
    public function getExludedShopFindingIds()
    {
        $allShopFindingIds = ShopFinding::withTrashed()->pluck('id')->toArray();
            
        $wildcards = [];
        $array = [];
        
        array_map(function($value) use (&$wildcards, &$array) {
            if (stripos($value, '*') === false) {
                $array[] = $value;
            } else {
                $wildcards[] = str_ireplace('*', '%', $value);
            }
        }, $this->getParts());
        
        $inSql = implode("','", $array);
        
        $inSql = "'" . $inSql . "'";
        
        $likeSql1 = '';
        $likeSql2 = '';
        
        if (count($wildcards)) {
            foreach ($wildcards as $wildcard) {
                $likeSql1 .= ' OR RCS_Segments.MPN LIKE "' . $wildcard . '"';
                $likeSql2 .= ' OR notifications.rcsMPN LIKE "' . $wildcard . '"';
            }
        }
        
        $sql1 = "(RCS_Segments.MPN IN ($inSql) $likeSql1)";
        $sql2 = "(notifications.rcsMPN IN ($inSql) $likeSql2)";
        
        return DB::table('shop_findings')
            ->select(
                'shop_findings.id',
                DB::raw("COALESCE(shop_findings.plant_code, notifications.plant_code) as plant_code")
            )
            ->leftJoin('notifications', 'shop_findings.id', '=', 'notifications.id')
            ->leftJoin('HDR_Segments', 'HDR_Segments.shop_finding_id', '=', 'shop_findings.id')
            ->leftJoin('RCS_Segments', 'RCS_Segments.SFI', '=', 'shop_findings.id')
            ->where(function($query) use ($sql1) {
                $query->whereNull('shop_findings.deleted_at')
                    ->whereRaw($sql1);
            })
            ->orWhere(function ($query) use ($allShopFindingIds, $sql2) {
                $query->whereIn('notifications.id', $allShopFindingIds)
                    ->whereNull('notifications.deleted_at')
                    ->whereRaw($sql2);
            })
            ->having('plant_code', $this->location->plant_code)
            ->pluck('id')
            ->toArray();
    }
}
