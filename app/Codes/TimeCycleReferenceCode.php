<?php

namespace App\Codes;

use App\Codes\Code;

class TimeCycleReferenceCode extends Code
{
    protected static $values = [
        'N' => 'N - Time/Cycles since last installation as new',
        'C' => 'C - Time/Cycles accumulated since last check',
        'I' => 'I - Time/Cycles since last installation',
        'O' => 'O - Time/Cycles since last overhaul',
        'R' => 'R - Time/Cycles since last repair',
        'V' => 'V - Time/Cycles since last shop visit',
        'X' => 'X - Time/Cycles since last inspection'
    ];
}
