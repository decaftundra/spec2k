<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

class ActionCode extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['RFI', 'SAC']; // Required to force accessors to work on json.
    
    public function scopeRfi($query, $RFI = NULL)
    {
        if (is_null($RFI)) return $query;
        
        return $query->where('RFI', $RFI)->orWhereNull('RFI');
    }
    
    public function scopeSac($query, $SAC = NULL)
    {
        if (!$SAC) return $query;
        return $query->where('SAC', $SAC);
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $RFI
     * @param (string) $SAC
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getActionCodes($RFI = NULL, $SAC = NULL)
    {
        return static::rfi($RFI)
            ->sac($SAC)
            ->get();
    }
    
    public function getRFIAttribute($value)
    {
        return $this->attributes['RFI'];
    }
    
    public function getSACAttribute($value)
    {
        return $this->attributes['SAC'];
    }
}
