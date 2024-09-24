<?php

namespace App\Codes;

use App\Codes\Code;
use Illuminate\Support\Facades\DB;

class EngineModelCode extends Code
{

    protected static $values = [];

    // LJMMar23 MGTSUP-373 filling the engine models.

    public static function getDropDownValues($selectMessage = 'Please select...')
    {
        $values = ['' => 'Empty - Please Select or type in the Other text box',];

        $tfwxEngineModels = DB::select(" SELECT * FROM engine_details order by engine_type, engines_series;");
        foreach ($tfwxEngineModels as $tfwxEngineModel)
        {
            $values[$tfwxEngineModel->engines_series] = $tfwxEngineModel->engines_series;
        }
        return array_unique($values);
    }

}


