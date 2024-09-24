<?php

use App\Location;
use Faker\Generator as Faker;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'sap_location_name' => $faker->unique()->country . $faker->company,
        'name' => $faker->unique()->country,
        'plant_code' => $faker->unique()->numerify('####'),
        'timezone' => $faker->timezone
    ];
});