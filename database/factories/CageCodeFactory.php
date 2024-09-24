<?php

use App\CageCode;
use Faker\Generator as Faker;

$factory->define(CageCode::class, function (Faker $faker) {
    return [
        'cage_code' => $faker->unique()->bothify('?##?#'),
        'info' => $faker->optional()->sentence
    ];
});
