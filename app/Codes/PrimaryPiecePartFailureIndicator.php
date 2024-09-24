<?php

namespace App\Codes;

use App\Codes\Code;

class PrimaryPiecePartFailureIndicator extends Code
{
    protected static $values = [
        'D' => 'D - Does Not Apply',
        'N' => 'N - No',
        'Y' => 'Y - Yes'
    ];
}