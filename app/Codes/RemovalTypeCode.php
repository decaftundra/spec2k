<?php

namespace App\Codes;

use App\Codes\Code;

class RemovalTypeCode extends Code
{
    protected static $values = [
        'U' => 'U - Unscheduled',
        'S' => 'S - Scheduled',
        'O' => 'O - Other'
    ];
}
