<?php

use App\Codes\UtasReasonCode;
use App\Codes\UtasCode;
use App\ShopFindings\MISC_Segment;
use App\ShopFindings\ShopFindingsDetail;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(MISC_Segment::class, function (Faker $faker) {
    
    return [
        'shop_findings_detail_id' => $faker->randomNumber,
        'values' => function() use ($faker) {
            
            $reasonCode = UtasReasonCode::inRandomOrder()->first();
            $utasCode = UtasCode::where('PLANT', $reasonCode->PLANT)->inRandomOrder()->first();
            
            $values = [
                'Type' => $reasonCode->TYPE,
                'Plant' => $reasonCode->PLANT,
                'PartNo' => $utasCode->MATNR,
                'Reason' => $reasonCode->REASON,
                'rcsSFI' => $faker->randomNumber,
                'Comments' => $faker->optional()->word,
                'Modifier' => $faker->optional()->word,
                'Component' => $utasCode->COMP,
                'plant_code' => $reasonCode->PLANT,
                'FeatureName' => $utasCode->FEAT,
                'SubassemblyName' => $utasCode->SUB,
                'FailureDescription' => $utasCode->DESCR
            ];
            
            return json_encode($values);
        },
        'is_valid' => $faker->boolean,
        'validated_at' => Carbon::now()->format('Y-m-d H:i:s')
    ];
});

/*
{
    "Type": "U",
    "Plant": "3101",
    "PartNo": "521225-1",
    "Reason": "INOPERATIVE",
    "rcsSFI": "000350374128",
    "Comments": null,
    "Modifier": null,
    "Component": "Captive bolt",
    "plant_code": "3101",
    "FeatureName": null,
    "SubassemblyName": "BODY",
    "FailureDescription": "damaged threads"
}
*/