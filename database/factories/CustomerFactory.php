<?php

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    
    $existingCodes = Customer::pluck('icao')->toArray();
    $icao = strtoupper($faker->unique()->lexify('???'));
    
    return [
        'company_name' => $faker->unique()->company,
        'icao' => in_array($icao, $existingCodes) ? NULL : $icao
    ];
});
