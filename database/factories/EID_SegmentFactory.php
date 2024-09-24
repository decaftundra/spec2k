<?php

use App\AircraftDetail;
use App\CageCode;
use Faker\Generator as Faker;
use App\Codes\EnginePositionCode;
use App\Codes\EngineModelCode;
use App\Codes\EngineTypeCode;
use App\ShopFindings\EID_Segment;
use Illuminate\Support\Str;

/*
EID = Engine Information

eidAET  Aircraft Engine Type                  Aircraft Engine/APU Type                AET Y   String  1/20    PW4000
eidEPC  Engine Position Code                  Engine Position Identifier              EPC Y   String  1/25    2
eidAEM  Aircraft Engine Model                 Aircraft Engine/APU Model               AEM Y   String  1/32    PW4056
eidEMS  Engine Serial Number                  Engine/APU Module Serial Number         EMS N   String  1/20    PCE-FA0006
eidMFR  Aircraft Engine Manufacturer Code     Manufacturer Code                       MFR N   String  5/5     77445
eidETH  Engine Cumulative Hours               Engine Cumulative Total Flight Hours    ETH N   Decimal 9,2
eidETC  Engine Cumulative Cycles              Engine Cumulative Total Cycles          ETC N   Integer 1/9
*/

$factory->define(EID_Segment::class, function (Faker $faker) {

  $enginePositionCodes = EnginePositionCode::getPermittedValues();
  $engineModelCodes = EngineModelCode::getPermittedValues();
  $engineTypeCodes = EngineTypeCode::getPermittedValues();

  $rand1and20 = rand(1, 20);

  // Any code except a Meggitt cage code or an Airframe cage code.
  $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());

  // Allow zzzzz and ZZZZZ.
  foreach ($cageCodes as $key => $val) {
    if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
      unset($cageCodes[$key]);
    }
  }

  do {
    $cageCode = Str::random(5);
  } while (in_array($cageCode, $cageCodes));

  // Ensure arrays are not empty before using array_rand
  $engineTypeCode = !empty($engineTypeCodes) ? (string) $engineTypeCodes[array_rand($engineTypeCodes)] : 'defaultTypeCode';
  $enginePositionCode = !empty($enginePositionCodes) ? (string) $enginePositionCodes[array_rand($enginePositionCodes)] : 'defaultPositionCode';
  $engineModelCode = !empty($engineModelCodes) ? (string) $engineModelCodes[array_rand($engineModelCodes)] : 'defaultModelCode';

  return [
    'AET' => $engineTypeCode,
    'EPC' => $enginePositionCode,
    'AEM' => $engineModelCode,
    'EMS' => $faker->boolean() ? Str::random($rand1and20) : NULL,
    'MFR' => $faker->boolean() ? $cageCode : NULL,
    'ETH' => $faker->optional()->randomFloat(2, 300, 9999999),
    'ETC' => $faker->optional()->numberBetween(1, 999999999),
  ];
});

$factory->state(EID_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {

  // Any code except a Meggitt cage code.
  $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());

  // Allow zzzzz and ZZZZZ.
  foreach ($cageCodes as $key => $val) {
    if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
      unset($cageCodes[$key]);
    }
  }

  do {
    $cageCode = Str::random(5);
  } while (in_array($cageCode, $cageCodes));

  return [
    'AET' => 'UNK',
    'EPC' => 'UNK',
    'AEM' => 'UNK',
    'EMS' => Str::random(20),
    'MFR' => $cageCode,
    'ETH' => 9999999.99,
    'ETC' => 999999999,
  ];
});

$factory->state(EID_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {

  // Any code except a Meggitt cage code.
  $cageCodes = array_merge(CageCode::getPermittedValues(), AircraftDetail::getPermittedValues());

  // Allow zzzzz and ZZZZZ.
  foreach ($cageCodes as $key => $val) {
    if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
      unset($cageCodes[$key]);
    }
  }

  do {
    $cageCode = Str::random(5);
  } while (in_array($cageCode, $cageCodes));

  return [
    'AET' => (string) '1',
    'EPC' => (string) '1',
    'AEM' => (string) '1',
    'EMS' => Str::random(1),
    'MFR' => $cageCode,
    'ETH' => 0.01,
    'ETC' => 1,
  ];
});
