<?php

use App\Codes\ActionCode;
use App\Codes\PartStatusCode;
use App\Codes\ShopActionCode;
use Faker\Generator as Faker;
use App\ShopFindings\SAS_Segment;
use App\Codes\ShopRepairFacilityCode;
use Illuminate\Support\Str;

/*
SAS = Shop Action Details

sasINT  Shop Action Text Incoming                   Inspection/Shop Action Text     INT     Y   String      1/5000
sasSHL  Shop Repair Location Code                   Shop Repair Facility Code       SHL     Y   String      1/3     R1
sasRFI  Shop Final Action Indicator                 Repair Final Action Indicator   RFI     Y   Boolean     1
sasMAT  Mod (S) Incorporated (This Visit) Text      Manufacturer Authority Text     MAT     N   String      1/40
sasSAC  Shop Action Code                            Shop Action Code                SAC     N   String      1/5     RPLC
sasSDI  Shop Disclosure Indicator                   Shop Disclosure Indicator       SDI     N   Boolean     0
sasPSC  Part Status Code                            Part Status Code                PSC     N   String      1/16    Overhauled
sasREM  Comment Text                                Remarks Text                    REM     N   String      1/1000
*/

$factory->define(SAS_Segment::class, function (Faker $faker) {
    
    $rfi = $faker->randomElement([1, 0]);
    $shopStatusCode = array_rand(ShopRepairFacilityCode::getDropDownValues(false));
    $validShopActionCodes = ActionCode::getActionCodes($rfi, $SAC = NULL)->pluck('SAC')->toArray();
    $shopActionCodeIndex = array_rand($validShopActionCodes);
    
    return [
    	'INT' => $faker->paragraph(),
    	'SHL' => $shopStatusCode,
    	'RFI' => $rfi,
    	'MAT' => trim(substr($faker->sentence(), 0, 40)),
    	'SAC' => $validShopActionCodes[$shopActionCodeIndex],
    	'SDI' => $faker->optional()->randomElement([1, 0]),
    	'PSC' => array_rand(PartStatusCode::getDropDownValues(false)),
    	'REM' => $faker->optional()->paragraph(),
    ];
});

$factory->state(SAS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    
    $rfi = $faker->randomElement([1, 0]);
    $shopStatusCode = array_rand(ShopRepairFacilityCode::getDropDownValues(false));
    $validShopActionCodes = ActionCode::getActionCodes($rfi, $SAC = NULL)->pluck('SAC')->toArray();
    $shopActionCodeIndex = array_rand($validShopActionCodes);
    
    return [
    	'INT' => Str::random(5000),
    	'SHL' => $shopStatusCode,
    	'RFI' => $faker->randomElement([1, 0]),
    	'MAT' => Str::random(40),
    	'SAC' => $validShopActionCodes[$shopActionCodeIndex],
    	'SDI' => $faker->randomElement([1, 0]),
    	'PSC' => array_rand(PartStatusCode::getDropDownValues(false)),
    	'REM' => Str::random(1000),
    ];
});

$factory->state(SAS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    
    $rfi = $faker->randomElement([1, 0]);
    $shopStatusCode = array_rand(ShopRepairFacilityCode::getDropDownValues(false));
    $validShopActionCodes = ActionCode::getActionCodes($rfi, $SAC = NULL)->pluck('SAC')->toArray();
    $shopActionCodeIndex = array_rand($validShopActionCodes);
    
    return [
    	'INT' => Str::random(1),
    	'SHL' => $shopStatusCode,
    	'RFI' => $faker->randomElement([1, 0]),
    	'MAT' => Str::random(1),
    	'SAC' => $validShopActionCodes[$shopActionCodeIndex],
    	'SDI' => $faker->randomElement([1, 0]),
    	'PSC' => array_rand(PartStatusCode::getDropDownValues(false)),
    	'REM' => Str::random(1),
    ];
});
