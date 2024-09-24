<?php

use App\CageCode;
use Faker\Generator as Faker;
use App\PieceParts\RPS_Segment;
use Illuminate\Support\Str;

/*
RPS = Replaced Piece Part

rpsMPN  Replaced Piece Part Manufacturer Full Length Part Number        Manufacturer Full Length Part Number    MPN     Y   String  1/32
rpsMFR  Replaced Piece Part Vendor Code                                 Manufacturer Code                       MFR     N   String  5/5
rpsMFN  Replaced Piece Part Vendor Name                                 Manufacturer Name                       MFN     N   String  1/55
rpsSER  Replaced Vendor Piece Part Serial Number                        Part Serial Number                      SER     N   String  1/15
rpsPNR  Replaced Vendor Piece Part Number                               Part Number                             PNR     N   String  1/15
rpsOPN  Overlength Part Number                                          Overlength Part Number                  OPN     N   String  16/32
rpsUSN  Replaced Piece Part Universal Serial Number                     Universal Serial Number                 USN     N   String  6/20
rpsASN  Replaced Operator Piece Part Number                             Airline Stock Number                    ASN     N   String  1/32
rpsUCN  Replaced Operator Piece Part Serial Number                      Unique Component Identification Number  UCN     N   String  1/15
rpsSPL  Supplier Code                                                   Supplier Code                           SPL     N   String  5/5
rpsUST  Replaced Piece Part Universal Serial Tracking Number            Universal Serial Tracking Number        UST     N   String  6/20
rpsPDT  Replaced Vendor Piece Part Description                          Part Description                        PDT     N   String  1/100
*/

$factory->define(RPS_Segment::class, function (Faker $faker) {
    
    $rand1and30 = rand(1,30);
    $rand6and20 = rand(6,20);
    $rand16and32 = rand(16,32);
    $rand1and15 = rand(1,15);
    $rand1and32 = rand(1,32);
    $rand1and55 = rand(1,55);
    $mpnAndOrPdt = mt_rand(1,3);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $ser = NULL;
    
    if ($faker->boolean()) {
        $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    }
    
    return [
        'MPN' => Str::random($rand1and32),
        'MFR' => $faker->boolean() ? $cageCodes[array_rand($cageCodes)] : NULL,
        'MFN' => trim(substr($faker->optional()->company(), 0, 55)),
        'SER' => $ser,
        'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
    ];
});

$factory->state(RPS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MPN' => Str::random(32),
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MFN' => Str::random(55),
        'SER' => Str::random(15),
        'PNR' => Str::random(15),
        'OPN' => Str::random(32),
        'USN' => Str::random(20),
        'ASN' => Str::random(32),
        'UCN' => Str::random(15),
        'SPL' => Str::random(5),
        'UST' => Str::random(20),
        'PDT' => Str::random(100),
    ];
});

$factory->state(RPS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'MPN' => Str::random(1),
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MFN' => Str::random(1),
        'SER' => Str::random(1),
        'PNR' => Str::random(1),
        'OPN' => Str::random(16),
        'USN' => Str::random(6),
        'ASN' => Str::random(1),
        'UCN' => Str::random(1),
        'SPL' => Str::random(5),
        'UST' => Str::random(6),
        'PDT' => Str::random(1),
    ];
});