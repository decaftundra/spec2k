<?php

namespace App\Codes;

use App\Codes\Code;

class FinalIndicatorCode extends Code
{
    protected static $values = [
        1 => 'The unit returned is re-certified',
        0 => 'The unit returned is returned as-is, kept for investigation or scrapped'
    ];
}