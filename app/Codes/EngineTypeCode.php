<?php

namespace App\Codes;

use App\Codes\Code;
use Illuminate\Support\Facades\DB;

class EngineTypeCode extends Code
{

    protected static $values = [];


    // LJMMar23 MGTSUP-373 filling the engine types.
    public static function getDropDownValues($selectMessage = 'Please select...')
    {
        $values = ['' => 'Empty - Please Select or type in the Other text box.',];

        $tfwxEngineTypes = DB::select(" SELECT DISTINCT(engine_type) FROM engine_details ORDER BY engine_type; ");
        foreach ($tfwxEngineTypes as $tfwxEngineType)
        {
            $values[$tfwxEngineType->engine_type] = $tfwxEngineType->engine_type;
        }
        return array_unique($values);
    }










}
