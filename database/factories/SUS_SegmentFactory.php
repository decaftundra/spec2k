<?php

use App\CageCode;
use App\UtasCode;
use App\Codes\PartStatusCode;
use Faker\Generator as Faker;
use App\ShopFindings\SUS_Segment;
use Illuminate\Support\Str;

/*
SUS = Shipped LRU

susSHD  Shipped Date                                    Shipped Date                                SHD     Y   Date        YYYY-MM-DD
susMFR  Shipped Part Manufacturer Code                  Manufacturer Code                           MFR     Y   String      5/5
susMPN  Shipped Manufacturer Full Length Part Number    Manufacturer Full Length Part Number        MPN     Y   String      1/32
susSER  Shipped Manufacturer Serial Number              Part Serial Number                          SER     Y   String      1/15
susMFN  Shipped Part Manufacturer Name                  Manufacturer Name                           MFN     N   String      1/55    Honeywell
susPDT  Shipped Manufacturer Part Description           Part Description                            PDT     N   String      1/100
susPNR  Shipped Manufacturer Part Number                Part Number                                 PNR     N   String      1/15
susOPN  Overlength Part Number                          Overlength Part Number                      OPN     N   String      16/32
susUSN  Shipped Universal Serial Number                 Universal Serial Number                     USN     N   String      6/20
susASN  Shipped Operator Part Number                    Airline Stock Number                        ASN     N   String      1/32
susUCN  Shipped Operator Serial Number                  Unique Component Identification Number      UCN     N   String      1/15
susSPL  Supplier Code                                   Supplier Code                               SPL     N   String      5/5
susUST  Shipped Universal Serial Tracking Number        Universal Serial Tracking Number            UST     N   String      6/20
susPML  Shipped Part Modification Level                 Part Modification Level                     PML     N   String      1/100
susPSC  Shipped Part Status Code                        Part Status Code                            PSC     N   String      1/16
*/

$factory->define(SUS_Segment::class, function (Faker $faker) {
    $rand1and15 = rand(1,15);
    $rand6and20 = rand(6,20);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    $partStatusCode = array_rand(PartStatusCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    $cageCode = $cageCodes[array_rand($cageCodes)];
    
    $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    
    if ($ser == 'ZZZZZ') {
        $ucn = Str::random($rand1and15);
    } else {
        $ucn = $faker->boolean() ? Str::random($rand1and15) : NULL;
    }
    
    return [
        'SHD' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
    	'MFR' => $cageCode,
    	'MPN' => Str::random($rand1and32),
    	'SER' => $ser,
    	'MFN' => in_array($cageCode, ['ZZZZZ', 'zzzzz']) ? trim(substr($faker->company(), 0, 55)) : trim(substr($faker->optional()->company(), 0, 55)),
    	'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
    	'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
    	'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
    	'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
    	'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
    	'UCN' => $ucn,
    	'SPL' => $faker->boolean() ? Str::random(5) : NULL,
    	'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
    	'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
    	'PSC' => $faker->boolean() ? $partStatusCode : NULL,
    ];
});

$factory->state(SUS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $partStatusCode = array_rand(PartStatusCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'SHD' => $faker->dateTimeBetween('-6 months', 'now'),
    	'MFR' => $cageCodes[array_rand($cageCodes)],
    	'MPN' => Str::random(32),
    	'SER' => Str::random(15),
    	'MFN' => Str::random(55),
    	'PDT' => Str::random(100),
    	'PNR' => Str::random(15),
    	'OPN' => Str::random(32),
    	'USN' => Str::random(20),
    	'ASN' => Str::random(32),
    	'UCN' => Str::random(15),
    	'SPL' => Str::random(5),
    	'UST' => Str::random(20),
    	'PML' => Str::random(100),
    	'PSC' => $partStatusCode,
    ];
});

$factory->state(SUS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $partStatusCode = array_rand(PartStatusCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'SHD' => $faker->dateTimeBetween('-6 months', 'now'),
    	'MFR' => $cageCodes[array_rand($cageCodes)],
    	'MPN' => Str::random(1),
    	'SER' => Str::random(1),
    	'MFN' => Str::random(1),
    	'PDT' => Str::random(1),
    	'PNR' => Str::random(1),
    	'OPN' => Str::random(16),
    	'USN' => Str::random(6),
    	'ASN' => Str::random(1),
    	'UCN' => Str::random(1),
    	'SPL' => Str::random(5),
    	'UST' => Str::random(6),
    	'PML' => Str::random(1),
    	'PSC' => $partStatusCode,
    ];
});

$factory->state(SUS_Segment::class, 'collins_part', function (Faker $faker) {
    $rand1and15 = rand(1,15);
    $rand6and20 = rand(6,20);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    $partStatusCode = array_rand(PartStatusCode::getDropDownValues(false));
    
    $ser = $faker->boolean(80) ? Str::random($rand1and15) : 'ZZZZZ';
    
    $cageCodes = CageCode::getPermittedValues();
    $cageCode = $cageCodes[array_rand($cageCodes)];
    
    if ($ser == 'ZZZZZ') {
        $ucn = Str::random($rand1and15);
    } else {
        $ucn = $faker->boolean() ? Str::random($rand1and15) : NULL;
    }
    
    return [
        'SHD' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
    	'MFR' => $cageCode,
    	'MPN' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
    	'SER' => $ser,
    	'MFN' => in_array($cageCode, ['ZZZZZ', 'zzzzz']) ? trim(substr($faker->company(), 0, 55)) : trim(substr($faker->optional()->company(), 0, 55)),
    	'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
    	'PNR' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
    	'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
    	'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
    	'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
    	'UCN' => $ucn,
    	'SPL' => $faker->boolean() ? Str::random(5) : NULL,
    	'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
    	'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
    	'PSC' => $faker->boolean() ? $partStatusCode : NULL,
    ];
});