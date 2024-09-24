<?php

namespace App\Codes;

use App\Codes\Code;

class ReasonForRemovalTypeCode extends Code
{
    protected static $values = [
        'BF' => 'BF - Bite Test Faulty',
        'BT' => 'BT - Bench Test',
        'CA' => 'CA - Calibration',
        'CL' => 'CL - Cycle Limit',
        'CO' => 'CO - Contamination',
        'DF' => 'DF - Defective From Stock',
        'DM' => 'DM - Damage',
        'DR' => 'DR - Developmental Repair',
        'ED' => 'ED - External Defect',
        'EP' => 'EP - Exchange Program',
        'FD' => 'FD - Functional Defect',
        'FI' => 'FI - Fault Isolation/Trouble Shooting',
        'FO' => 'FO - Foreign Object Damage',
        'GN' => 'GN - Go/No-Go Test',
        'HT' => 'HT - Hydraulic Test',
        'LK' => 'LK - Leakage',
        'LT' => 'LT - Leak Test',
        'MD' => 'MD - Modification',
        'OH' => 'OH - Overhaul',
        'OT' => 'OT - Overtemp',
        'PC' => 'PC - Pilot Complaint',
        'PF' => 'PF - Failure/Inop',
        'QD' => 'QD - Quality Defect',
        'RC' => 'RC - Reconditioning',
        'RF' => 'RF - Refurbishing',
        'RP' => 'RP - Repair',
        'RS' => 'RS - Restoration',
        'SM' => 'SM - Scheduled Maintenance',
        'SR' => 'SR - Shelf Life Renewal',
        'TL' => 'TL - Time Limit',
        'ZZ' => 'ZZ - Requires "Reason for Removal Clarification Text"'
    ];
}
