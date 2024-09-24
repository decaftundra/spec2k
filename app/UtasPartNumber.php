<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UtasPartNumber extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Get the Utas part number using given Meggitt part number.
     *
     * @param (string) $meggittPartNo
     * @return mixed string|null
     */
    public static function getUtasPartNo($meggittPartNo)
    {
        $part = self::where('meggitt_part_no', $meggittPartNo)->first();
        
        return $part ? $part->utas_part_no : NULL;
    }
    
    /**
     * Get the Meggitt part number using given Utas part number.
     *
     * @param (string) $utasPartNo
     * @return mixed string|null
     */
    public static function getMeggittPartNo($utasPartNo)
    {
        $part = self::where('utas_part_no', $utasPartNo)->first();
        
        return $part ? $part->meggitt_part_no : NULL;
    }
}
