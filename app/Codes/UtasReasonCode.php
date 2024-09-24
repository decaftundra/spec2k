<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

class UtasReasonCode extends Model
{
    /**
     |-----------------------------
     | Dynamic Local Scopes
     |-----------------------------
     */
    
    public function scopePlant($query, $plant = NULL)
    {
        if (!$plant) return $query;
        return $query->where('PLANT', $plant);
    }
    
    public function scopeType($query, $type = NULL)
    {
        if (!$type) return $query;
        return $query->where('TYPE', $type);
    }
    
    public function scopeReason($query, $reason = NULL)
    {
        if (!$reason) return $query;
        return $query->where('REASON', $reason);
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $plant
     * @param (string) $type
     * @param (string) $reason
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getUtasReasonCodes($plant = NULL, $type = NULL, $reason = NULL)
    {
        return static::plant($plant)
            ->type($type)
            ->reason($reason)
            ->get();
    }
}
