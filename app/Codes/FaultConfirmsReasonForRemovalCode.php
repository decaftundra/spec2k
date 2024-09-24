<?php

namespace App\Codes;

use App\Codes\Code;

class FaultConfirmsReasonForRemovalCode extends Code
{
    protected static $values = [
        'NA' => 'NA - Not Applicable',
        'CR' => 'CR - Confirmed',
        'NC' => 'NC - Not Confirmed'
    ];
}
