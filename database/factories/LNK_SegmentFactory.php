<?php

use Faker\Generator as Faker;
use App\ShopFindings\LNK_Segment;
use Illuminate\Support\Str;

/*
LNK = Linking Fields

lnkRTI  Removal Tracking Identifier     Removal Tracking Identifier     RTI     Y   String      1/50
*/

$factory->define(LNK_Segment::class, function (Faker $faker) {
    return [
        'RTI' => Str::random(rand(1, 50))
    ];
});

$factory->state(LNK_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    return [
        'RTI' => Str::random(50)
    ];
});

$factory->state(LNK_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    return [
        'RTI' => Str::random(1)
    ];
});