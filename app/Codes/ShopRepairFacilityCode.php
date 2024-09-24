<?php

namespace App\Codes;

use App\Codes\Code;

class ShopRepairFacilityCode extends Code
{
    protected static $values = [
        'R2' => 'R2 - Repaired at OEM',
        'R1' => 'R1 - Repaired at airline shop',
        'R3' => 'R3 - Repaired at third-party facility'
    ];
}