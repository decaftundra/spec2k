<?php

use App\Location;
use App\PartList;
use Faker\Generator as Faker;

$factory->define(PartList::class, function (Faker $faker) {
    
    $location = Location::inRandomOrder()->first();
    $contexts = PartList::$contexts;
    
    $parts = [];
    $count = $faker->randomDigitNotNull;
    
    while ($count > 0) {
        $parts[] = $faker->randomNumber;
        $count--;
    }
    
    return [
        'location_id' => $location->id,
        //'context' => $contexts[array_rand($contexts)],
        'context' => 'exclude', // Only exclude part lists allowed at this point.
        'parts' => implode(', ', $parts)
    ];
});
