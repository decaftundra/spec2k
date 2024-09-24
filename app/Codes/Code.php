<?php

namespace App\Codes;

use Illuminate\Database\Eloquent\Model;

abstract class Code extends Model
{
    /**
     * Array of codes and names.
     *
     * @var array
     */
    protected static $values = [];
    
    /**
     * Get an array of the permitted values.
     *
     * @return array
     */
    public static function getPermittedValues()
    {
        return array_keys(static::$values);
    }
    
    /**
     * Get the array to populate the form select options.
     *
     * @param (string) $selectMessage
     * @return array
     */
    public static function getDropDownValues($selectMessage = 'Please select...')
    {
        if (!$selectMessage) return static::$values;
        
        $select = ['' => $selectMessage];
        
        return array_replace($select, static::$values);
    }
}
