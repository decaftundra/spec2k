<?php

namespace App\Codes;

use App\Codes\Code;

class SupplierRemovalTypeCode extends Code
{
    protected static $values = [
        'U' => 'U - Unscheduled',
        'S' => 'S - Scheduled',
        'M' => 'M - Modification',
        'P' => 'P - Production return (OEM)',
        'O' => 'O - Other (Not to be used without TC Holder Agreement)'
    ];
}
