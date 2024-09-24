<?php

use Carbon\Carbon;
use Faker\Generator as Faker;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;

$factory->define(ShopFindingsDetail::class, function (Faker $faker) {
    return [
        'shop_finding_id' => function () {
            return factory(ShopFinding::class)->lazy();
        },
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ];
});