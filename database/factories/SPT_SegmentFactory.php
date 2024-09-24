<?php

use Faker\Generator as Faker;
use App\ShopFindings\SPT_Segment;

/*
SPT = Shop Processing Time

sptMAH  Shop Total Labor Hours      Total Labor Hours               MAH     N   Decimal     8,2     110.00
sptFLW  Shop Flow Time              Shop Flow Time                  FLW     N   Integer     1/9
sptMST  Shop Turn Around Time       Mean Shop Processing Time       MST     N   Integer     1/4
*/

$factory->define(SPT_Segment::class, function (Faker $faker) {
    
    $filledField = mt_rand(1,3);
    
    return [
        'MAH' => $filledField == 1 ? $faker->randomFloat(2, 0, 999999) : $faker->optional()->randomFloat(2, 0, 999999),
        'FLW' => $filledField == 2 ? $faker->numberBetween(0, 999999999) : $faker->optional()->numberBetween(0, 999999999),
        'MST' => $filledField == 3 ? $faker->numberBetween(0, 9999) : $faker->optional()->numberBetween(0, 9999),
    ];
});

$factory->state(SPT_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    return [
        'MAH' => 999999.99,
        'FLW' => 999999999,
        'MST' => 9999,
    ];
});

$factory->state(SPT_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    return [
        'MAH' => 0.01,
        'FLW' => 1,
        'MST' => 1,
    ];
});