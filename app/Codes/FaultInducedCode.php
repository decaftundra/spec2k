<?php

namespace App\Codes;

use App\Codes\Code;

class FaultInducedCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'IN' => 'IN - Induced',
        'NI' => 'NI - Not Induced'
    ];
}
