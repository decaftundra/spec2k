<?php

use Faker\Generator as Faker;
use App\ShopFindings\ATT_Segment;
use App\Codes\TimeCycleReferenceCode;

/*
ATT = Accumulated Time Text (Removed LRU)

attTRF  Time/Cycle Reference Code       Time/Cycle Reference Code       TRF     Y   String      1/1
attOTT  Operating Time                  Operating Time                  OTT     N   Integer     1/6
attOPC  Operating Cycle Count           Operating Cycle Count           OPC     N   Integer     1/6
attODT  Operating Day Count             Operating Days                  ODT     N   Integer     1/6
*/

$factory->define(ATT_Segment::class, function (Faker $faker) {
    
    // At least one of OTT, OPC and ODT needs a value.
    $randomNumber = mt_rand(1, 3);
    $keys = ['OTT', 'OPC', 'ODT'];
    $keysWithValues = (array) array_rand($keys, $randomNumber);
    
    return [
        'TRF' => array_rand(TimeCycleReferenceCode::getDropDownValues(false)),
        'OTT' => in_array(0, $keysWithValues) ? $faker->numberBetween(1, 999999) : NULL,
        'OPC' => in_array(1, $keysWithValues) ? $faker->numberBetween(1, 999999) : NULL,
        'ODT' => in_array(2, $keysWithValues) ? $faker->numberBetween(1, 999999) : NULL,
    ];
});

$factory->state(ATT_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    return [
        'TRF' => array_rand(TimeCycleReferenceCode::getDropDownValues(false)),
        'OTT' => 999999,
        'OPC' => 999999,
        'ODT' => 999999,
    ];
});

$factory->state(ATT_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    return [
        'TRF' => array_rand(TimeCycleReferenceCode::getDropDownValues(false)),
        'OTT' => 1,
        'OPC' => 1,
        'ODT' => 1,
    ];
});