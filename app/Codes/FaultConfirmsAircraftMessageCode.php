<?php

namespace App\Codes;

use App\Codes\Code;

class FaultConfirmsAircraftMessageCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'CM' => 'CM - Confirms Aircraft Message',
        'NM' => 'NM - Doesn\'t Confirm Aircraft Message'
    ];
}
