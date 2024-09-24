<?php

use App\SAPAccessToken;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

$factory->define(SAPAccessToken::class, function (Faker $faker) {
    return [
    	'token' => Str::random(30),
    	'scope' => '',
    	'created_at' => Carbon::now(),
    	'updated_at' => Carbon::now(),
    	'expires_at' => 216000,
    ];
});