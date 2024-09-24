<?php

namespace App\Codes;

use App\Codes\Code;

class FaultConfirmsAircraftPartBiteMessageCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'CB' => 'CB - Confirms Aircraft Bite Message',
        'NB' => 'NB - Doesn\'t Confirm Aircraft Bite Message'
    ];
}
