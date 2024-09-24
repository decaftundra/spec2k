<?php

use App\CageCode;
use Faker\Generator as Faker;
use App\ShopFindings\AID_Segment;
use App\AircraftDetail;
use Illuminate\Support\Str;

/*
AID = Airframe Information

aidMFR  Airframe Manufacturer Code              Manufacturer Code                               MFR     Y   String  5/5     S4956
aidAMC  Aircraft Model                          Aircraft Model Identifier                       AMC     Y   String  1/20    757
aidMFN  Airframe Manufacturer Name              Manufacturer Name                               MFN     N   String  1/55    EMBRAER
aidASE  Aircraft Series                         Aircraft Series Identifier                      ASE     N   String  3/10    300F
aidAIN  Aircraft Manufacturer Serial Number     Aircraft Identification Number                  AIN     N   String  1/10    25398
aidREG  Aircraft Registration Number            Aircraft Fully Qualified Registration Number    REG     N   String  1/10    N550UA
aidOIN  Operator Aircraft Internal Identifier   Operator Aircraft Internal Identifier           OIN     N   String  1/10    550
aidCTH  Aircraft Cumulative Total Flight Hours  Aircraft Cumulative Total Flight Hours          CTH     N   Decimal 9,2     10015.00
aidCTY  Aircraft Cumulative Total Cycles        Aircraft Cumulative Total Cycles                CTY     N   Integer 1/9     5025
*/

$factory->define(AID_Segment::class, function (Faker $faker) {
    $random3and10 = rand(3,10);
    $random1and10 = rand(1,10);
    
    // At least one of OTT, OPC and ODT needs a value.
    $randomNumber = mt_rand(1, 3);
    $keys = ['AIN', 'REG', 'OIN'];
    $keysWithValues = (array) array_rand($keys, $randomNumber);
    $cageCodes = AircraftDetail::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'AMC' => trim(substr($faker->company, 0, 20)),
        'MFN' => $faker->boolean() ? trim(substr($faker->company, 0, 55)) : NULL,
        'ASE' => $faker->boolean() ? Str::random($random3and10) : NULL,
        'AIN' => in_array(0, $keysWithValues) ? Str::random($random1and10) : NULL,
        'REG' => in_array(1, $keysWithValues) ? Str::random($random1and10) : NULL,
        'OIN' => in_array(2, $keysWithValues) ? Str::random($random1and10) : NULL,
        'CTH' => $faker->optional()->randomFloat(2, 300, 9999999),
        'CTY' => $faker->optional()->numberBetween(1, 999999999),
    ];
});

$factory->state(AID_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    $cageCodes = AircraftDetail::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'AMC' => Str::random(20),
        'MFN' => Str::random(55),
        'ASE' => Str::random(10),
        'AIN' => Str::random(10),
        'REG' => Str::random(10),
        'OIN' => Str::random(10),
        'CTH' => 9999999.99,
        'CTY' => 999999999,
    ];
});

$factory->state(AID_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    $cageCodes = AircraftDetail::getPermittedValues();
    
    return [
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'AMC' => Str::random(1),
        'MFN' => Str::random(1),
        'ASE' => Str::random(3),
        'AIN' => Str::random(1),
        'REG' => Str::random(1),
        'OIN' => Str::random(1),
        'CTH' => 0.01,
        'CTY' => 1,
    ];
});

$factory->state(AID_Segment::class, 'with_real_aircraft_data', function (Faker $faker) {
    
    // Get a unique aircraft from the data.
    do {
      $randomReg = AircraftDetail::inRandomOrder()->first()->aircraft_fully_qualified_registration_no;
      $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $randomReg)->get();
    } while (count($aircraft) > 1);
    
    return [
        'MFR' => substr($aircraft[0]->manufacturer_code, 0, 5),
        'AMC' => substr($aircraft[0]->aircraft_model_identifier, 0, 20),
        'MFN' => substr($aircraft[0]->manufacturer_name, 0, 55),
        'ASE' => substr($aircraft[0]->aircraft_series_identifier, 0, 10),
        'AIN' => substr($aircraft[0]->aircraft_identification_no, 0, 10),
        'REG' => substr($aircraft[0]->aircraft_fully_qualified_registration_no, 0, 10),
        'OIN' => Str::random(1),
        'CTH' => 0.01,
        'CTY' => 1,
    ];
});