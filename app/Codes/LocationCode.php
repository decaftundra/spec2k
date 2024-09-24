<?php

namespace App\Codes;

use App\Codes\Code;

class LocationCode extends Code
{
    protected static $values = [
        'R1' => 'R1 - Repaired at Airline Shop',
        'R2' => 'R2 - Repaired at OEM',
        'R3' => 'R3 - Repaired at 3rd Party Facility'
    ];
}
