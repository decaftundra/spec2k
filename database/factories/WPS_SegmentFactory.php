<?php

use App\CageCode;
use Faker\Generator as Faker;
use App\PieceParts\WPS_Segment;
use App\Codes\PrimaryPiecePartFailureIndicator;
use Illuminate\Support\Str;

/*
WPS = Worked Piece Part

wpsSFI  Shop Finding Record Identifier                              Shop Findings Record Identifier         SFI     Y   String  1/50
wpsPPI  Piece Part Record Identifier                                Piece Part Record Identifier            PPI     Y   String  1/50
wpsPFC  Primary Piece Part Failure Indicator                        Primary Piece Part Failure Indicator	PFC     Y   String  1/1	Y
wpsMFR  Failed Piece Part Vendor Code                               Manufacturer Code                       MFR     N   String  5/5
wpsMFN  Failed Piece Part Vendor Name                               Manufacturer Name                       MFN     N   String  1/55
wpsMPN  Failed Piece Part Manufacturer Full Length Part Number      Manufacturer Full Length Part Number    MPN     N   String  1/32
wpsSER  Failed Piece Part Serial Number                             Part Serial Number                      SER     N   String  1/15
wpsFDE  Piece Part Failure Description                              Piece Part Failure Description          FDE     N   String  1/1000
wpsPNR  Vendor Piece Part Number                                    Part Number                             PNR     N   String  1/15
wpsOPN  Overlength Part Number                                      Overlength Part Number                  OPN     N   String  16/32
wpsUSN  Piece Part Universal Serial Number                          Universal Serial Number                 USN     N   String  6/20
wpsPDT  Failed Piece Part Description                               Part Description                        PDT     N   String  1/100
wpsGEL  Piece Part Reference Designator Symbol                      Geographic and/or Electrical Location   GEL     N   String  1/30
wpsMRD  Received Date                                               Material Receipt Date                   MRD     N   Date    YYYY-MM-DD
wpsASN  Operator Piece Part Number                                  Airline Stock Number                    ASN     N   String  1/32
wpsUCN  Operator Piece Part Serial Number                           Unique Component Identification Number  UCN     N   String  1/15
wpsSPL  Supplier Code                                               Supplier Code                           SPL     N   String  5/5
wpsUST  Piece Part Universal Serial Tracking Number                 Universal Serial Tracking Number        UST     N   String  6/20
*/

$factory->define(WPS_Segment::class, function (Faker $faker) {
    $rand1and30 = rand(1,30);
    $rand6and20 = rand(6,20);
    $rand16and32 = rand(16,32);
    $rand1and15 = rand(1,15);
    $rand1and32 = rand(1,32);
    $rand1and55 = rand(1,55);
    $mpnAndOrPdt = mt_rand(1,3);
    
    $cageCodes = CageCode::getPermittedValues();
    
    $ser = NULL;
    
    if ($faker->boolean(80)) {
        $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    }
    
    return [
        'SFI' => (string) $faker->unique()->numberBetween(1, 9999999999),
        'PPI' => (string) $faker->unique()->numberBetween(1, 9999999999),
        'PFC' => array_rand(PrimaryPiecePartFailureIndicator::getDropDownValues(false)),
        'MFR' => $faker->boolean() ? $cageCodes[array_rand($cageCodes)] : NULL,
        'MFN' => $faker->boolean() ? Str::random($rand1and55) : NULL,
        'MPN' => $mpnAndOrPdt != 2 ? Str::random($rand1and32) : NULL,
        'SER' => $ser,
        'FDE' => trim(substr($faker->optional()->paragraph(), 0, 1000)),
        'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => $mpnAndOrPdt != 1 ? trim(substr($faker->sentence(), 0, 100)) : NULL,
        'GEL' => $faker->boolean() ? Str::random($rand1and30) : NULL,
        'MRD' => $faker->optional()->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
    ];
});

$factory->state(WPS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $sfi = (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5);
    
    $sfi = $sfi . $sfi;
    
    $ppi = (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5);
    
    $ppi = $ppi . $ppi;
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'SFI' => (string) $sfi,
        'PPI' => (string) $ppi,
        'PFC' => array_rand(PrimaryPiecePartFailureIndicator::getDropDownValues(false)),
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MFN' => Str::random(55),
        'MPN' => Str::random(32),
        'SER' => Str::random(15),
        'FDE' => Str::random(1000),
        'PNR' => Str::random(15),
        'OPN' => Str::random(32),
        'USN' => Str::random(20),
        'PDT' => Str::random(100),
        'GEL' => Str::random(30),
        'MRD' => $faker->dateTimeBetween('-6 months', 'now'),
        'ASN' => Str::random(32),
        'UCN' => Str::random(15),
        'SPL' => Str::random(5),
        'UST' => Str::random(20),
    ];
});

$factory->state(WPS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'SFI' => (string) $faker->unique()->numberBetween(1, 9),
        'PPI' => (string) $faker->unique()->numberBetween(1, 9),
        'PFC' => array_rand(PrimaryPiecePartFailureIndicator::getDropDownValues(false)),
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MFN' => Str::random(1),
        'MPN' => Str::random(1),
        'SER' => Str::random(1),
        'FDE' => Str::random(1),
        'PNR' => Str::random(1),
        'OPN' => Str::random(16),
        'USN' => Str::random(6),
        'PDT' => Str::random(1),
        'GEL' => Str::random(1),
        'MRD' => $faker->optional()->dateTimeBetween('-6 months', 'now'),
        'ASN' => Str::random(1),
        'UCN' => Str::random(1),
        'SPL' => Str::random(5),
        'UST' => Str::random(6),
    ];
});