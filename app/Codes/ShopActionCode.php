<?php

namespace App\Codes;

use App\Codes\Code;

class ShopActionCode extends Code
{
    protected static $values = [
        'IRTR' => 'IRTR - Incomplete Repair - transferred to another facility',
        'SCRP' => 'SCRP - Scrapped',
        'XCHG' => 'XCHG - Exchanged',
        'RPLC' => 'RPLC - Replaced',
        'RTAS' => 'RTAS - Returned As Is - no repair performed',
        'RCRT' => 'RCRT - Recertify',
        'REPR' => 'REPR - Repaired',
        'BERP' => 'BERP - Beyond Economic Repair',
        'CLBN' => 'CLBN - Calibrated',
        'MODN' => 'MODN - Modified',
        'OVHL' => 'OVHL - Overhauled',
        'REFN' => 'REFN - Refinish/Replate',
        'RLSW' => 'RLSW - Reloaded Software',
        'ROMP' => 'ROMP - Repaired per Operator maintenance program',
        'RPCK' => 'RPCK - Repack/Reseal',
        'RWRK' => 'RWRK - Reworked',
        'SADJ' => 'SADJ - Adjust',
        'SLRN' => 'SLRN - Shelf Life Renewal',
        'SPAG' => 'SPAG - Special Agreement (Code not used alone)',
        'TEST' => 'TEST - Tested',
        'UNRP' => 'UNRP - Unrepairable',
        
    ];
}
