<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

class UtasCode extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['DESCR', 'FEAT', 'SUB', 'COMP']; // Required to force accessors to work on json.
    
    /**
     |-----------------------------
     | Dynamic Local Scopes
     |-----------------------------
     */
    
    public function scopePlantCode($query, $plantCode = NULL)
    {
        if (!$plantCode) return $query;
        return $query->where('PLANT', $plantCode);
    }
    
    public function scopePartNo($query, $partNo = NULL)
    {
        if (!$partNo) return $query;
        return $query->where('MATNR', $partNo);
    }
    
    public function scopeSubAssemblyName($query, $subAssemblyName = NULL)
    {
        if (!$subAssemblyName) return $query;
        return $query->where('SUB', $subAssemblyName);
    }
    
    public function scopeComponent($query, $component = NULL)
    {
        if (!$component) return $query;
        return $query->where('COMP', $component);
    }
    
    public function scopeFeature($query, $feature = NULL)
    {
        if (!$feature) return $query;
        return $query->where('FEAT', $feature);
    }
    
    public function scopeDescription($query, $description = NULL)
    {
        if (!$description) return $query;
        return $query->where('DESCR', $description);
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
    public static function getUtasCodes(
        $plantCode = NULL,
        $partNo = NULL,
        $subassemblyName = NULL,
        $component = NULL,
        $feature = NULL,
        $description = NULL
    )
    {
        return static::plantCode($plantCode)
            ->partNo($partNo)
            ->subAssemblyName($subassemblyName)
            ->component($component)
            ->feature($feature)
            ->description($description)
            ->get();
    }
    
    public function getDescrAttribute($value)
    {
        return strtolower($this->attributes['DESCR']);
    }
    
    public function getFeatAttribute($value)
    {
        return strtolower($this->attributes['FEAT']);
    }
    
    public function getSubAttribute($value)
    {
        return strtoupper($this->attributes['SUB']);
    }
    
    public function getCompAttribute($value)
    {
        return ucfirst($this->attributes['COMP']);
    }
}
