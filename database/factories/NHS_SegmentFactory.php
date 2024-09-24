<?php

use App\CageCode;
use App\UtasCode;
use Faker\Generator as Faker;
use App\PieceParts\NHS_Segment;
use Illuminate\Support\Str;

/*
NHS = Next Higher Assembly

nhsMFR  Failed Piece Part Next Higher Assembly Part Manufacturer Code   Manufacturer Code                                           MFR Y   String  5/5
nhsMPN  Next Higher Assembly Manufacturer Full Length Part Number       Manufacturer Full Length Part Number                        MPN Y   String  1/32
nhsSER  Failed Piece Part Next Higher Assembly Serial Number            Part Serial Number                                          SER Y   String  1/15
nhsMFN  Failed Piece Part Next Higher Assembly Part Manufacturer Name   Manufacturer Name                                           MFN N   String  1/55
nhsPNR  Failed Piece Part Next Higher Assembly Part Number              Part Number                                                 PNR N   String  1/15
nhsOPN  Overlength Part Number                                          Overlength Part Number                                      OPN N   String  16/32
nhsUSN  Failed Piece Part Universal Serial Number                       Universal Serial Number                                     USN N   String  6/20
nhsPDT  Failed Piece Part Next Higher Assembly Part Name                Part Description                                            PDT N   String  1/100
nhsASN  Failed Piece Part Next Higher Assembly Operator Part Number     Airline Stock Number                                        ASN N   String  1/32
nhsUCN  Failed Piece Part Next Higher Assembly Operator Serial Number   Unique Component Identification Number                      UCN N   String  1/15
nhsSPL  Supplier Code                                                   Supplier Code                                               SPL N   String  5/5
nhsUST  Failed Piece Part NHA Universal Serial Tracking Number          Universal Serial Tracking Number                            UST N   String  6/20
nhsNPN  Failed Piece Part Next Higher Assembly NHA Part Number          Failed Piece Part Next Higher Assembly NHA Part Number      NPN N   String  1/32
*/

$factory->define(NHS_Segment::class, function (Faker $faker) {
    
    $rand1and30 = rand(1,30);
    $rand6and20 = rand(6,20);
    $rand16and32 = rand(16,32);
    $rand1and15 = rand(1,15);
    $rand1and32 = rand(1,32);
    $rand1and55 = rand(1,55);
    $mpnAndOrPdt = mt_rand(1,3);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $mfr = $faker->boolean(80) ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    
    $mfn = NULL;
    
    if (($mfr == 'ZZZZZ') || ($mfr == 'zzzzz')) {
        $mfn = Str::random($rand1and55);
    } else {
        $mfn = $faker->boolean() ? Str::random($rand1and55) : NULL;
    }
    
    return [
        'MFR' => $mfr,
        'MPN' => Str::random($rand1and32),
        'SER' => $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ',
        'MFN' => $mfn,
        'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'NPN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
    ];
});

$factory->state(NHS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MPN' => Str::random(32),
        'SER' => Str::random(15),
        'MFN' => Str::random(55),
        'PNR' => Str::random(15),
        'OPN' => Str::random(32),
        'USN' => Str::random(20),
        'PDT' => Str::random(100),
        'ASN' => Str::random(32),
        'UCN' => Str::random(15),
        'SPL' => Str::random(5),
        'UST' => Str::random(20),
        'NPN' => Str::random(32),
    ];
});

$factory->state(NHS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MPN' => Str::random(1),
        'SER' => Str::random(1),
        'MFN' => Str::random(1),
        'PNR' => Str::random(1),
        'OPN' => Str::random(16),
        'USN' => Str::random(6),
        'PDT' => Str::random(1),
        'ASN' => Str::random(1),
        'UCN' => Str::random(1),
        'SPL' => Str::random(5),
        'UST' => Str::random(6),
        'NPN' => Str::random(1),
    ];
});

$factory->state(NHS_Segment::class, 'collins_part', function (Faker $faker) {
    
    $rand1and30 = rand(1,30);
    $rand6and20 = rand(6,20);
    $rand16and32 = rand(16,32);
    $rand1and15 = rand(1,15);
    $rand1and32 = rand(1,32);
    $rand1and55 = rand(1,55);
    $mpnAndOrPdt = mt_rand(1,3);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $mfr = $faker->boolean(80) ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    
    $mfn = NULL;
    
    if (($mfr == 'ZZZZZ') || ($mfr == 'zzzzz')) {
        $mfn = Str::random($rand1and55);
    } else {
        $mfn = $faker->boolean() ? Str::random($rand1and55) : NULL;
    }
    
    return [
        'MFR' => $mfr,
        'MPN' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'SER' => $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ',
        'MFN' => $mfn,
        'PNR' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'NPN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
    ];
});