<?php

use Carbon\Carbon;
use App\PieceParts\PiecePart;
use Faker\Generator as Faker;
use App\ShopFindings\ShopFinding;

$factory->define(PiecePart::class, function (Faker $faker) {
    return [
        'shop_finding_id' => function () {
            return factory(ShopFinding::class)->lazy();
        },
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ];
});