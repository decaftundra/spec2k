<?php

use Carbon\Carbon;
use App\HDR_Segment;
use App\CageCode;
use App\Customer;
use App\Location;
use App\UtasCode;
use App\Codes\ChangeCode;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
hdrCHG  Record Status                       Change Code                     CHG     Y   String  1/1         N
hdrROC  Reporting Organization Code         Reporting Organization Code     ROC     Y   String  3/5         58960
hdrRDT  Reporting Period Start Date         Reporting Period Date           RDT     Y   Date    2001-07-01
hdrRSD  Reporting Period End Date           Reporting Period End Date       RSD     Y   Date    2001-07-31
hdrOPR  Operator Code                       Operator Code                   OPR     Y   String  3/5         UAL
hdrRON  Reporting Organization Name         Reporting Organization Name     RON     N   String  1/55        Honeywell
hdrWHO  Operator Name                       Company Name                    WHO     N   String  1/55        United Airlines
*/

$factory->define(HDR_Segment::class, function (Faker $faker) {
    
    $changeCodes = ChangeCode::getPermittedValues();
    $airline = Customer::inRandomOrder()->first();
    $date = Carbon::now();
    
    if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isUser())) {
        $location = Location::where('id', auth()->user()->location_id)->first();
    } else {
        $location = Location::inRandomOrder()->first();
    }
    
    $cageCodes = CageCode::getPermittedValues();
    
    $location = auth()->check() ? Location::where('id', auth()->user()->location_id)->first() : Location::inRandomOrder()->first();
    $cageCode = auth()->check() ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    
    return [
        'CHG' => $changeCodes[array_rand($changeCodes)],
    	'ROC' => $cageCode,
    	'RDT' => NULL,
    	'RSD' => NULL,
    	'OPR' => $airline->icao ?: 'ZZZZZ',
    	'RON' => in_array($cageCode, ['ZZZZZ', 'zzzzz']) ? $faker->company : $location->name,
    	'WHO' => !$airline->icao ? trim(substr($airline->company_name, 0, 55)) : $airline->company_name,
    ];
});

$factory->state(HDR_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    $changeCodes = ChangeCode::getPermittedValues();
    
    if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isUser())) {
        $location = Location::where('id', auth()->user()->location_id)->first();
    } else {
        $location = Location::inRandomOrder()->first();
    }
    
    $cageCodes = CageCode::getPermittedValues();
    
    $location = auth()->check() ? Location::where('id', auth()->user()->location_id)->first() : Location::inRandomOrder()->first();
    $cageCode = auth()->check() ? $cageCodes[array_rand($cageCodes)] : 'ZZZZZ';
    
    return [
        'CHG' => $changeCodes[array_rand($changeCodes)],
    	'ROC' => $cageCode,
    	'RDT' => NULL,
    	'RSD' => NULL,
    	'OPR' => 'ZZZZZ',
    	'RON' => Str::random(55),
    	'WHO' => Str::random(55),
    ];
});

$factory->state(HDR_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    $changeCodes = ChangeCode::getPermittedValues();
    $cageCodes = CageCode::getPermittedValues();
    
    return [
        'CHG' => $changeCodes[array_rand($changeCodes)],
    	'ROC' => $cageCodes[array_rand($cageCodes)],
    	'RDT' => NULL,
    	'RSD' => NULL,
    	'OPR' => Str::random(3),
    	'RON' => Str::random(1),
    	'WHO' => Str::random(1),
    ];
});

$factory->state(HDR_Segment::class, 'collins_part', function (Faker $faker) {
    
    $changeCodes = ChangeCode::getPermittedValues();
    $airline = Customer::inRandomOrder()->first();
    $date = Carbon::now();
    
    $utasCode = UtasCode::inRandomOrder()->first();
    $location = Location::with('cage_codes')->where('plant_code', $utasCode->PLANT)->inRandomOrder()->first();
    
    return [
        'CHG' => $changeCodes[array_rand($changeCodes)],
    	'ROC' => $location->cage_codes->random(1)->first()->cage_code,
    	'RDT' => NULL,
    	'RSD' => NULL,
    	'OPR' => $airline->icao ?: 'ZZZZZ',
    	'RON' => $location->name,
    	'WHO' => !$airline->icao ? trim(substr($airline->company_name, 0, 55)) : $airline->company_name,
    ];
});