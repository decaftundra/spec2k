<?php

namespace App\Codes;

use App\Codes\Code;

class ChangeCode extends Code
{
    protected static $values = [
        'N' => 'N - New',
        'D' => 'D - Delete',
        'T' => 'T - Total Replacement'
    ];
}
