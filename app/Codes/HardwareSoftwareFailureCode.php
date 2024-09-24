<?php

namespace App\Codes;

use App\Codes\Code;

class HardwareSoftwareFailureCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'HW' => 'HW - Hardware Failure',
        'SW' => 'SW - Software Failure'
    ];
}
