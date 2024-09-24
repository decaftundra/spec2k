<?php

namespace App\Codes;

use App\UtasReasonCode;
use App\Codes\Code;

class PlantCode extends Code
{
    /**
     * Get an array of the permitted values.
     * Overrides the same function in Code abstract class.
     *
     * @return array
     */
    public static function getPermittedValues()
    {
        $values = UtasReasonCode::pluck('PLANT')->toArray();
        
        return array_unique($values);
    }
}