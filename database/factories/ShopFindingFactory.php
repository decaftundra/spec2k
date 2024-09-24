<?php

use Carbon\Carbon;
use App\Location;
use App\UtasCode;
use Faker\Generator as Faker;
use App\ShopFindings\ShopFinding;

$factory->define(ShopFinding::class, function (Faker $faker) {
    return [
        'id' => (string) $faker->unique()->numberBetween(1, 9999999999),
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'ataID' => 'R2009.1',
        'ataVersion' => 1,
        'SFVersion' => 2,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL,
        'deleted_at' => NULL,
    ];
});

$factory->state(ShopFinding::class, 'all_fields_max_string_length', function (Faker $faker) {
    $id = (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5);
    
    $id = $id . $id;
    
    return [
        'id' => (string) $id,
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'ataID' => 'R2009.1',
        'ataVersion' => '1.0',
        'SFVersion' => '2.00',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL,
        'deleted_at' => NULL,
    ];
});

$factory->state(ShopFinding::class, 'all_fields_min_string_length', function (Faker $faker) {
    return [
        'id' => (string) $faker->unique()->numberBetween(1, 9),
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'ataID' => 'R2009.1',
        'ataVersion' => '1.0',
        'SFVersion' => '2.00',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL,
        'deleted_at' => NULL,
    ];
});

$factory->state(ShopFinding::class, 'collins_part', function (Faker $faker) {
    return [
        'id' => (string) $faker->unique()->numberBetween(1, 9999999999),
        'plant_code' => function(){
            return UtasCode::inRandomOrder()->first()->PLANT;
        },
        'ataID' => 'R2009.1',
        'ataVersion' => 1,
        'SFVersion' => 2,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL,
        'deleted_at' => NULL,
    ];
});