<?php

use App\MaintenanceNotice;
use Faker\Generator as Faker;

$factory->define(MaintenanceNotice::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'contents' => $faker->paragraph,
        'display' => $faker->boolean
    ];
});
