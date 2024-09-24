<?php

use App\AircraftDetail;
use App\CageCode;
use Faker\Generator as Faker;
use App\ShopFindings\API_Segment;
use Illuminate\Support\Str;

/*
API = APU Information

apiAET  Aircraft APU Type                       Aircraft Engine/APU Type            AET     Y   String      1/20    331-400B
apiEMS  APU Serial Number                       Engine/APU Module Serial Number     EMS     Y   String      1/20    SP-E994180
apiAEM  Aircraft APU Model                      Aircraft Engine/APU Model           AEM     N   String      1/32    3800608-2
apiMFR  Aircraft Engine Manufacturer Code       Manufacturer Code                   MFR     N   String      5/5     99193
apiATH  APU Cumulative Hours                    APU Cumulative Total Hours          ATH     N   Decimal     9,2
apiATC  APU Cumulative Cycles                   APU Cumulative Total Cycles         ATC     N   Integer     1/9
*/

$factory->define(API_Segment::class, function (Faker $faker) {
    $rand1and20 = rand(1,20);
    $rand1and32 = rand(1,32);
    
    // Any code except a Meggitt cage code or an Airframer cage code.
    $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());
    
    // Allow zzzzz and ZZZZZ.
    foreach ($cageCodes as $key => $val) {
        if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
            unset($cageCodes[$key]);
        }
    }
    
    do {
        $cageCode = Str::random(5);
    } while (in_array($cageCode, $cageCodes));
    
    return [
        'AET' => Str::random($rand1and20),
    	'EMS' => Str::random($rand1and20),
    	'AEM' => $faker->boolean() ? Str::random($rand1and32) : NULL,
    	'MFR' => $faker->boolean() ? $cageCode : NULL,
    	'ATH' => $faker->optional()->randomFloat(2, 300, 9999999),
    	'ATC' => $faker->optional()->numberBetween(1, 999999999),
    ];
});

$factory->state(API_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());
    
    // Allow zzzzz and ZZZZZ.
    foreach ($cageCodes as $key => $val) {
        if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
            unset($cageCodes[$key]);
        }
    }
    
    do {
        $cageCode = Str::random(5);
    } while (in_array($cageCode, $cageCodes));
    
    return [
        'AET' => Str::random(20),
    	'EMS' => Str::random(20),
    	'AEM' => Str::random(32),
    	'MFR' => $cageCode,
    	'ATH' => 9999999.99,
    	'ATC' => 999999999,
    ];
});

$factory->state(API_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());
    
    // Allow zzzzz and ZZZZZ.
    foreach ($cageCodes as $key => $val) {
        if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
            unset($cageCodes[$key]);
        }
    }
    
    do {
        $cageCode = Str::random(5);
    } while (in_array($cageCode, $cageCodes));
    
    return [
        'AET' => Str::random(1),
    	'EMS' => Str::random(1),
    	'AEM' => Str::random(1),
    	'MFR' => $cageCode,
    	'ATH' => 0.01,
    	'ATC' => 1,
    ];
});