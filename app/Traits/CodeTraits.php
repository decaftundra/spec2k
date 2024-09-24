<?php

namespace App\Traits;

trait CodeTraits
{
    /**
     * Get the array to populate the form select options.
     *
     * @param (string) $selectMessage
     * @return array
     */
    public static function getDropDownValues($selectMessage = 'Please select...')
    {
        $values = self::pluck('name', 'code')->toArray();
        
        if (!$selectMessage) {
            return $values;
        }
        
        $select = ['' => $selectMessage];
        
        return array_replace($select, $values);
    }
}