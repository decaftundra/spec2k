<?php

use App\CageCode;
use App\UtasCode;
use Faker\Generator as Faker;
use App\Codes\RemovalTypeCode;
use App\ShopFindings\RLS_Segment;
use App\Codes\ReasonForRemovalTypeCode;
use Illuminate\Support\Str;

/*
RLS = Removed LRU

rlsMFR  Removed Part Manufacturer Code                  Manufacturer Code                           MFR     Y   String      5/5
rlsMPN  Removed Manufacturer Full Length Part Number    Manufacturer Full Length Part Number        MPN     Y   String      1/32
rlsSER  Removed Manufacturer Serial Number              Part Serial Number                          SER     Y   String      1/15
rlsRED  Removal Date                                    Part Removal Date                           RED     N   Date        YYYY-MM-DD
rlsTTY  Removal Type Code                               Removal Type Code                           TTY     N   String      1/1	S
rlsRET  Removal Type Text                               Reason for Removal Clarification Text       RET     N   String      1/64
rlsDOI  Install Date of Removed Part                    Installation Date                           DOI     N   Date        2001-06-01
rlsMFN  Removed Part Manufacturer Name                  Manufacturer Name                           MFN     N   String      1/55        Honeywell
rlsPNR  Removed Manufacturer Part Number                Part Number                                 PNR     N   String      1/15
rlsOPN  Overlength Part Number                          Overlength Part Number                      OPN     N   String      16/32
rlsUSN  Removed Universal Serial Number                 Universal Serial Number                     USN     N   String      6/20
rlsRMT  Removal Reason Text                             Removal Reason Text                         RMT     N   String      1/5000
rlsAPT  Engine/APU Position Identifier                  Aircraft Engine/APU Position Text           APT     N   String      1/100
rlsCPI  Part Position Code                              Component Position Code                     CPI     N   String      1/25        LB061
rlsCPT  Part Position                                   Component Position Text                     CPT     N   String      1/100       Passenger door sect 15
rlsPDT  Removed Part Description                        Part Description                            PDT     N   String      1/100
rlsPML  Removed Part Modification Level                 Part Modification Level                     PML     N   String      1/100
rlsASN  Removed Operator Part Number                    Airline Stock Number                        ASN     N   String      1/32
rlsUCN  Removed Operator Serial Number                  Unique Component Identification Number      UCN     N   String      1/15
rlsSPL  Supplier Code                                   Supplier Code                               SPL     N   String      5/5
rlsUST  Removed Universal Serial Tracking Number        Universal Serial Tracking Number            UST     N   String      6/20
rlsRFR  Removal Reason Code                             Reason for Removal Code                     RFR     N   String      2/2
*/

$factory->define(RLS_Segment::class, function (Faker $faker) {
    $rand1and15 = rand(1,15);
    $rand1and20 = rand(1,20);
    $rand6and20 = rand(6,20);
    $rand1and25 = rand(1,25);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $removalTypeCode = array_rand(RemovalTypeCode::getDropDownValues(false));
    $reasonForRemovalTypeCode = array_rand(ReasonForRemovalTypeCode::getDropDownValues(false));
    $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    $mfr = $faker->boolean(80) ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    $tty = $faker->boolean() ? $removalTypeCode : NULL;
    $rfr = $faker->boolean() ? $reasonForRemovalTypeCode : NULL;
    
    if (($tty == 'O') || ($rfr == 'ZZ')) {
        $ret = Str::random(64);
    } else {
        $ret = trim(substr($faker->optional()->sentence(), 0, 64));
    }
    
    return [
        'MFR' => $mfr,
        'MPN' => Str::random($rand1and32),
        'SER' => $ser,
        'RED' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
        'TTY' => $tty,
        'RET' => $ret,
        'DOI' => $faker->optional()->dateTimeBetween($startDate = '-3 years', $endDate = '-1 year'),
        'MFN' => in_array($mfr, ['ZZZZZ', 'zzzzz']) ? trim(substr($faker->company(), 0, 55)) : trim(substr($faker->optional()->company(), 0, 55)),
        'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RMT' => trim(substr($faker->optional()->paragraph(), 0, 5000)),
        'APT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'CPI' => $faker->boolean() ? Str::random($rand1and25) : NULL,
        'CPT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => in_array($ser, ['ZZZZZ', 'zzzzz']) || $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RFR' => $rfr,
    ];
});

$factory->state(RLS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    $removalTypeCode = array_rand(RemovalTypeCode::getDropDownValues(false));
    $reasonForRemovalTypeCode = array_rand(ReasonForRemovalTypeCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MPN' => Str::random(32),
        'SER' => Str::random(15),
        'RED' => $faker->dateTimeBetween('-6 months', 'now'),
        'TTY' => $removalTypeCode,
        'RET' => Str::random(64),
        'DOI' => $faker->optional()->dateTimeBetween('-3 years', '-1 year'),
        'MFN' => Str::random(55),
        'PNR' => Str::random(15),
        'OPN' => Str::random(32),
        'USN' => Str::random(20),
        'RMT' => Str::random(5000),
        'APT' => Str::random(100),
        'CPI' => Str::random(25),
        'CPT' => Str::random(100),
        'PDT' => Str::random(100),
        'PML' => Str::random(100),
        'ASN' => Str::random(32),
        'UCN' => Str::random(15),
        'SPL' => Str::random(5),
        'UST' => Str::random(20),
        'RFR' => $reasonForRemovalTypeCode,
    ];
});

$factory->state(RLS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    $removalTypeCode = array_rand(RemovalTypeCode::getDropDownValues(false));
    $reasonForRemovalTypeCode = array_rand(ReasonForRemovalTypeCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MPN' => Str::random(1),
        'SER' => Str::random(1),
        'RED' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
        'TTY' => $removalTypeCode,
        'RET' => Str::random(1),
        'DOI' => $faker->optional()->dateTimeBetween('-3 years', '-1 year'),
        'MFN' => Str::random(1),
        'PNR' => Str::random(1),
        'OPN' => Str::random(16),
        'USN' => Str::random(6),
        'RMT' => Str::random(1),
        'APT' => Str::random(1),
        'CPI' => Str::random(1),
        'CPT' => Str::random(1),
        'PDT' => Str::random(1),
        'PML' => Str::random(1),
        'ASN' => Str::random(1),
        'UCN' => Str::random(1),
        'SPL' => Str::random(5),
        'UST' => Str::random(6),
        'RFR' => $reasonForRemovalTypeCode,
    ];
});

$factory->state(RLS_Segment::class, 'collins_part', function (Faker $faker) {
    $rand1and15 = rand(1,15);
    $rand1and20 = rand(1,20);
    $rand6and20 = rand(6,20);
    $rand1and25 = rand(1,25);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $removalTypeCode = array_rand(RemovalTypeCode::getDropDownValues(false));
    $reasonForRemovalTypeCode = array_rand(ReasonForRemovalTypeCode::getDropDownValues(false));
    $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    $mfr = $faker->boolean(80) ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    $tty = $faker->boolean() ? $removalTypeCode : NULL;
    $rfr = $faker->boolean() ? $reasonForRemovalTypeCode : NULL;
    
    if (($tty == 'O') || ($rfr == 'ZZ')) {
        $ret = Str::random(64);
    } else {
        $ret = trim(substr($faker->optional()->sentence(), 0, 64));
    }
    
    return [
        'MFR' => $mfr,
        'MPN' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'SER' => $ser,
        'RED' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
        'TTY' => $tty,
        'RET' => $ret,
        'DOI' => $faker->optional()->dateTimeBetween($startDate = '-3 years', $endDate = '-1 year'),
        'MFN' => $mfr == 'ZZZZZ' ? trim(substr($faker->company(), 0, 55)) : trim(substr($faker->optional()->company(), 0, 55)),
        'PNR' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RMT' => trim(substr($faker->optional()->paragraph(), 0, 5000)),
        'APT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'CPI' => $faker->boolean() ? Str::random($rand1and25) : NULL,
        'CPT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => in_array($ser, ['ZZZZZ', 'zzzzz']) || $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RFR' => $rfr,
    ];
});