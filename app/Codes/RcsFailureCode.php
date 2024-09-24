<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

class RcsFailureCode extends Model
{
    /**
     |-----------------------------
     | Dynamic Local Scopes
     |-----------------------------
     */
    
    public function scopeUtas($query, $utas = NULL)
    {
        if (!$utas) return $query;
        return $query->whereIn('RRC', ['U', 'S']);
    }
    
    public function scopeRrc($query, $rrc = NULL)
    {
        if (!$rrc) return $query;
        return $query->where('RRC', $rrc);
    }
    
    public function scopeFfc($query, $ffc = NULL)
    {
        if (!$ffc) return $query;
        return $query->where('FFC', $ffc);
    }
    
    public function scopeFfi($query, $ffi = NULL)
    {
        if (!$ffi) return $query;
        return $query->where('FFI', $ffi);
    }
    
    public function scopeFhs($query, $fhs = NULL)
    {
        if (!$fhs) return $query;
        return $query->where('FHS', $fhs);
    }
    
    public function scopeFcr($query, $fcr = NULL)
    {
        if (!$fcr) return $query;
        return $query->where('FCR', $fcr);
    }
    
    public function scopeFac($query, $fac = NULL)
    {
        if (!$fac) return $query;
        return $query->where('FAC', $fac);
    }
    
    public function scopeFbc($query, $fbc = NULL)
    {
        if (!$fbc) return $query;
        return $query->where('FBC', $fbc);
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $rrc
     * @param (string) $ffc
     * @param (string) $ffi
     * @param (string) $fhs
     * @param (string) $fcr
     * @param (string) $fac
     * @param (string) $fbc
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getRcsFailureCodes(
        $utas = NULL,
        $rrc = NULL,
        $ffc = NULL,
        $ffi = NULL,
        $fhs = NULL,
        $fcr = NULL,
        $fac = NULL,
        $fbc = NULL
    )
    {
        return static::utas($utas)
            ->rrc($rrc)
            ->ffc($ffc)
            ->ffi($ffi)
            ->fhs($fhs)
            ->fcr($fcr)
            ->fac($fac)
            ->fbc($fbc)
            ->get();
    }
}
