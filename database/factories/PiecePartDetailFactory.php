<?php

use Carbon\Carbon;
use App\PieceParts\PiecePart;
use Faker\Generator as Faker;
use App\PieceParts\PiecePartDetail;

$factory->define(PiecePartDetail::class, function (Faker $faker) {
    return [
        'id' => (string) $faker->unique()->numberBetween(1, 9999999999),
        'piece_part_id' => function () {
            return factory(PiecePart::class)->lazy();
        },
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ];
});