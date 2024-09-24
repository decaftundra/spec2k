<?php

namespace App\Codes;

use App\Codes\Code;

class FaultFoundCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'FT' => 'FT - Failed Test / Inspection',
        'NT' => 'NT - No Trouble Found'
    ];
}