<?php

use App\CageCode;
use App\UtasCode;
use Faker\Generator as Faker;
use App\Codes\FaultFoundCode;
use App\Codes\FaultInducedCode;
use App\ShopFindings\RCS_Segment;
use App\Codes\SupplierRemovalTypeCode;
use App\Codes\HardwareSoftwareFailureCode;
use App\Codes\FaultConfirmsAircraftMessageCode;
use App\Codes\FaultConfirmsReasonForRemovalCode;
use App\Codes\FaultConfirmsAircraftPartBiteMessageCode;
use Illuminate\Support\Str;

/*
RCS = Received LRU

SFI  Shop Findings Record Identifier                         Shop Findings Record Identifier                 SFI     Y   String  1/50
MRD  Shop Received Date                                      Material Receipt Date                           MRD     Y   Date    YYYY-MM-DD
MFR  Received Part Manufacturer Code                         Manufacturer Code                               MFR     Y   String  5/5
MPN  Received Manufacturer Full Length Part Number           Manufacturer Full Length Part Number            MPN     Y   String  1/32
SER  Received Manufacturer Serial Number                     Part Serial Number                              SER     Y   String  1/15
RRC  Supplier Removal Type Code                              Supplier Removal Type Code                      RRC     Y   String  1/1     S
FFC  Failure/ Fault Found                                    Failure/Fault Found Code                        FFC     Y   String  1/2     FT
FFI  Failure/ Fault Induced                                  Failure/Fault Induced Code                      FFI     Y   String  1/2     NI
FCR  Failure/ Fault Confirms Reason For Removal              Failure/Fault Confirm Reason Code               FCR     Y   String  1/2     CR
FAC  Failure/ Fault Confirms Aircraft Message                Failure/Fault Confirm Aircraft Message Code     FAC     Y   String  1/2     NA
FBC  Failure/ Fault Confirms Aircraft Part Bite Message      Failure/Fault Confirm Bite Message Code         FBC     Y   String  1/2     NB
FHS  Hardware/Software Failure                               Hardware/Software Failure Code                  FHS     Y   String  1/2     SW
MFN  Removed Part Manufacturer Name                          Manufacturer Name                               MFN     N   String  1/55    Honeywell
PNR  Received Manufacturer Part Number                       Part Number                                     PNR     N   String  1/15
OPN  Overlength Part Number                                  Overlength Part Number                          OPN     N   String  16/32
USN  Removed Universal Serial Number                         Universal Serial Number                         USN     N   String  6/20
RET  Supplier Removal Type Text                              Reason for Removal Clarification Text           RET     N   String  1/64
CIC  Customer Code                                           Customer Identification Code                    CIC     N   String  3/5     UAL
CPO  Repair Order Identifier                                 Customer Order Number                           CPO     N   String  1/11    123UA13
PSN  Packing Sheet Number                                    Packing Sheet Number                            PSN     N   String  1/15    123UA13PS1
WON  Work Order Number                                       Work Order Number                               WON     N   String  1/20    123UA13WO1
MRN  Maintenance Release Authorization Number                Maintenance Release Authorization Number        MRN     N   String  1/32    123UA13MR1
CTN  Contract Number                                         Contract Number                                 CTN     N   String  4/15    123UA13CT1
BOX  Master Carton Number                                    Master Carton Number                            BOX     N   String  1/10    123UA13BX1
ASN  Received Operator Part Number                           Airline Stock Number                            ASN     N   String  1/32
UCN  Received Operator Serial Number                         Unique Component Identification Number          UCN     N   String  1/15
SPL  Supplier Code                                           Supplier Code                                   SPL     N   String  5/5
UST  Removed Universal Serial Tracking Number                Universal Serial Tracking Number                UST     N   String  6/20
PDT  Manufacturer Part Description                           Part Description                                PDT     N   String  1/100
PML  Removed Part Modificiation Level                        Part Modification Level                         PML     N   String  1/100
SFC  Shop Findings Code                                      Shop Findings Code                              SFC     N   String  1/10
RSI  Related Shop Finding Record Identifier                  Related Shop Findings Record Identifier         RSI     N   String  1/50
RLN  Repair Location Name                                    Repair Location Name                            RLN     N   String  1/25
INT  Incoming Inspection Text                                Incoming Inspection/Shop Action Text            INT     N   String  1/5000
REM  Comment Text                                            Remarks Text                                    REM     N   String  1/1000
*/

$factory->define(RCS_Segment::class, function (Faker $faker) {
    $rand3and5 = rand(3,5);
    $rand1and10 = rand(1,10);
    $rand1and11 = rand(1,11);
    $rand4and15 = rand(4,15);
    $rand1and15 = rand(1,15);
    $rand1and20 = rand(1,20);
    $rand6and20 = rand(6,20);
    $rand1and25 = rand(1,25);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    $rand1and50 = rand(1,50);
    $rand1and55 = rand(1,55);
    $rand1and64 = rand(1,64);
    
    $supplierRemovalType = array_rand(SupplierRemovalTypeCode::getDropDownValues(false));
    $failureFaultFound = array_rand(FaultFoundCode::getDropDownValues(false));
    $failureFaultInduced = array_rand(FaultInducedCode::getDropDownValues(false));
    $hardwareSoftwareFailure = array_rand(HardwareSoftwareFailureCode::getDropDownValues(false));
    $faultConfirmsReasonForRemoval = array_rand(FaultConfirmsReasonForRemovalCode::getDropDownValues(false));
    $faultConfirmsAircraftMessage = array_rand(FaultConfirmsAircraftMessageCode::getDropDownValues(false));
    $faultConfirmsAircraftPartBiteMessageCode = array_rand(FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    $cageCode = $cageCodes[array_rand($cageCodes)];
    
    if ($failureFaultFound != 'FT') {
        if ($supplierRemovalType == 'M') {
            $failureFaultFound = 'NA';
            
        } else {
            $failureFaultFound = 'NT';
        }
        
        $failureFaultInduced = 'NA';
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $failureFaultInduced = $faker->boolean() ? 'NI' : 'IN';
    }
    
    if ($failureFaultInduced != 'NI') {
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $hardwareSoftwareFailure = $faker->boolean() ? 'HW' : 'SW';
    }
    
    if ($hardwareSoftwareFailure != 'NA') {
        if (in_array($supplierRemovalType, ['S','M'])) {
            $faultConfirmsReasonForRemoval = 'NA';
            $faultConfirmsAircraftMessage = 'NA';
            $faultConfirmsAircraftPartBiteMessageCode = 'NA';
        } else {
            $faultConfirmsReasonForRemoval = $faker->boolean() ? 'CR' : 'NC';
        }
    } else {
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    if ($faultConfirmsReasonForRemoval == 'NC') {
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    $mfn = NULL;
    
    if (in_array($cageCode, ['ZZZZZ', 'zzzzz'])) {
        $mfn = trim(substr($faker->company, 0, 55));
    } else if ($faker->boolean()) {
        $mfn = trim(substr($faker->company, 0, 55));
    }
        
    return [
        'SFI' => (string) $faker->unique()->numberBetween(100000, 9999999999),
        'MRD' => $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now'),
        'MFR' => $cageCode,
        'MPN' => Str::random(rand(7,32)),
        'SER' => Str::random(rand(7,15)),
        'RRC' => $supplierRemovalType,
        'FFC' => $failureFaultFound,
        'FFI' => $failureFaultInduced,
        'FCR' => $faultConfirmsReasonForRemoval,
        'FAC' => $faultConfirmsAircraftMessage,
        'FBC' => $faultConfirmsAircraftPartBiteMessageCode,
        'FHS' => $hardwareSoftwareFailure,
        'MFN' => $mfn,
        'PNR' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RET' => $faker->boolean() ? Str::random($rand1and64) : NULL,
        'CIC' => $faker->boolean() ? Str::random($rand3and5) : NULL,
        'CPO' => $faker->boolean() ? Str::random($rand1and11) : NULL,
        'PSN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'WON' => $faker->boolean() ? Str::random($rand1and20) : NULL,
        'MRN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'CTN' => $faker->boolean() ? Str::random($rand4and15) : NULL,
        'BOX' => $faker->boolean() ? Str::random($rand1and10) : NULL,
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'SFC' => $faker->boolean() ? Str::random($rand1and10) : NULL,
        'RSI' => $faker->boolean() ? Str::random($rand1and50) : NULL,
        'RLN' => $faker->boolean() ? Str::random($rand1and25) : NULL,
        'INT' => $faker->optional()->paragraph(),
        'REM' => $faker->optional()->paragraph()
    ];
});

$factory->state(RCS_Segment::class, 'invalid_FFC', function ($faker) {
    return [
        'SFI' => $faker->unique()->numberBetween(100000, 9999999999),
        'MRD' => $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now'),
        'MFR' => NULL, // invalid
        'MPN' => '241-251-000-121',
        'SER' => 'EB251',
        'RRC' => 'U',
        'FFC' => 'FT',
        'FFI' => 'NA', // invalid
        'FCR' => 'NA',
        'FAC' => 'NA',
        'FBC' => 'NA',
        'FHS' => 'NA',
    ];
});

$factory->state(RCS_Segment::class, 'all_fields_max_string_length', function (Faker $faker) {
    $supplierRemovalType = array_rand(SupplierRemovalTypeCode::getDropDownValues(false));
    $failureFaultFound = array_rand(FaultFoundCode::getDropDownValues(false));
    $failureFaultInduced = array_rand(FaultInducedCode::getDropDownValues(false));
    $hardwareSoftwareFailure = array_rand(HardwareSoftwareFailureCode::getDropDownValues(false));
    $faultConfirmsReasonForRemoval = array_rand(FaultConfirmsReasonForRemovalCode::getDropDownValues(false));
    $faultConfirmsAircraftMessage = array_rand(FaultConfirmsAircraftMessageCode::getDropDownValues(false));
    $faultConfirmsAircraftPartBiteMessageCode = array_rand(FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    $cageCode = $cageCodes[array_rand($cageCodes)];
    
    if ($failureFaultFound != 'FT') {
        if ($supplierRemovalType == 'M') {
            $failureFaultFound = 'NA';
            
        } else {
            $failureFaultFound = 'NT';
        }
        
        $failureFaultInduced = 'NA';
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $failureFaultInduced = $faker->boolean() ? 'NI' : 'IN';
    }
    
    if ($failureFaultInduced != 'NI') {
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $hardwareSoftwareFailure = $faker->boolean() ? 'HW' : 'SW';
    }
    
    if ($hardwareSoftwareFailure != 'NA') {
        if (in_array($supplierRemovalType, ['S','M'])) {
            $faultConfirmsReasonForRemoval = 'NA';
            $faultConfirmsAircraftMessage = 'NA';
            $faultConfirmsAircraftPartBiteMessageCode = 'NA';
        } else {
            $faultConfirmsReasonForRemoval = $faker->boolean() ? 'CR' : 'NC';
        }
    } else {
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    if ($faultConfirmsReasonForRemoval == 'NC') {
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    $sfi = (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5) . (string) $faker->randomNumber(5);
    
    $sfi = $sfi . $sfi;
        
    return [
        'SFI' => $sfi,
        'MRD' => $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now'),
        'MFR' => $cageCode,
        'MPN' => Str::random(32),
        'SER' => Str::random(15),
        'RRC' => $supplierRemovalType,
        'FFC' => $failureFaultFound,
        'FFI' => $failureFaultInduced,
        'FCR' => $faultConfirmsReasonForRemoval,
        'FAC' => $faultConfirmsAircraftMessage,
        'FBC' => $faultConfirmsAircraftPartBiteMessageCode,
        'FHS' => $hardwareSoftwareFailure,
        'MFN' => Str::random(55),
        'PNR' => Str::random(15),
        'OPN' => Str::random(32),
        'USN' => Str::random(20),
        'RET' => Str::random(64),
        'CIC' => Str::random(5),
        'CPO' => Str::random(11),
        'PSN' => Str::random(15),
        'WON' => Str::random(20),
        'MRN' => Str::random(32),
        'CTN' => Str::random(15),
        'BOX' => Str::random(10),
        'ASN' => Str::random(32),
        'UCN' => Str::random(15),
        'SPL' => Str::random(5),
        'UST' => Str::random(20),
        'PDT' => Str::random(100),
        'PML' => Str::random(100),
        'SFC' => Str::random(10),
        'RSI' => Str::random(50),
        'RLN' => Str::random(25),
        'INT' => Str::random(5000),
        'REM' => Str::random(1000)
    ];
});

$factory->state(RCS_Segment::class, 'all_fields_min_string_length', function (Faker $faker) {
    $supplierRemovalType = array_rand(SupplierRemovalTypeCode::getDropDownValues(false));
    $failureFaultFound = array_rand(FaultFoundCode::getDropDownValues(false));
    $failureFaultInduced = array_rand(FaultInducedCode::getDropDownValues(false));
    $hardwareSoftwareFailure = array_rand(HardwareSoftwareFailureCode::getDropDownValues(false));
    $faultConfirmsReasonForRemoval = array_rand(FaultConfirmsReasonForRemovalCode::getDropDownValues(false));
    $faultConfirmsAircraftMessage = array_rand(FaultConfirmsAircraftMessageCode::getDropDownValues(false));
    $faultConfirmsAircraftPartBiteMessageCode = array_rand(FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    
    if ($failureFaultFound != 'FT') {
        if ($supplierRemovalType == 'M') {
            $failureFaultFound = 'NA';
            
        } else {
            $failureFaultFound = 'NT';
        }
        
        $failureFaultInduced = 'NA';
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $failureFaultInduced = $faker->boolean() ? 'NI' : 'IN';
    }
    
    if ($failureFaultInduced != 'NI') {
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $hardwareSoftwareFailure = $faker->boolean() ? 'HW' : 'SW';
    }
    
    if ($hardwareSoftwareFailure != 'NA') {
        if (in_array($supplierRemovalType, ['S','M'])) {
            $faultConfirmsReasonForRemoval = 'NA';
            $faultConfirmsAircraftMessage = 'NA';
            $faultConfirmsAircraftPartBiteMessageCode = 'NA';
        } else {
            $faultConfirmsReasonForRemoval = $faker->boolean() ? 'CR' : 'NC';
        }
    } else {
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    if ($faultConfirmsReasonForRemoval == 'NC') {
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
        
    return [
        'SFI' => (string) $faker->numberBetween(1, 9),
        'MRD' => $faker->dateTimeBetween('-3 years', 'now'),
        'MFR' => $cageCodes[array_rand($cageCodes)],
        'MPN' => Str::random(7),
        'SER' => Str::random(7),
        'RRC' => $supplierRemovalType,
        'FFC' => $failureFaultFound,
        'FFI' => $failureFaultInduced,
        'FCR' => $faultConfirmsReasonForRemoval,
        'FAC' => $faultConfirmsAircraftMessage,
        'FBC' => $faultConfirmsAircraftPartBiteMessageCode,
        'FHS' => $hardwareSoftwareFailure,
        'MFN' => Str::random(1),
        'PNR' => Str::random(1),
        'OPN' => Str::random(16),
        'USN' => Str::random(6),
        'RET' => Str::random(1),
        'CIC' => Str::random(3),
        'CPO' => Str::random(1),
        'PSN' => Str::random(1),
        'WON' => Str::random(1),
        'MRN' => Str::random(1),
        'CTN' => Str::random(4),
        'BOX' => Str::random(1),
        'ASN' => Str::random(1),
        'UCN' => Str::random(1),
        'SPL' => Str::random(5),
        'UST' => Str::random(6),
        'PDT' => Str::random(1),
        'PML' => Str::random(1),
        'SFC' => Str::random(1),
        'RSI' => Str::random(1),
        'RLN' => Str::random(1),
        'INT' => Str::random(1),
        'REM' => Str::random(1),
    ];
});

$factory->state(RCS_Segment::class, 'collins_part', function (Faker $faker) {
    $rand3and5 = rand(3,5);
    $rand1and10 = rand(1,10);
    $rand1and11 = rand(1,11);
    $rand4and15 = rand(4,15);
    $rand1and15 = rand(1,15);
    $rand1and20 = rand(1,20);
    $rand6and20 = rand(6,20);
    $rand1and25 = rand(1,25);
    $rand1and32 = rand(1,32);
    $rand16and32 = rand(16,32);
    $rand1and50 = rand(1,50);
    $rand1and55 = rand(1,55);
    $rand1and64 = rand(1,64);
    
    $supplierRemovalType = $faker->boolean ? 'U' : 'S';
    $failureFaultFound = array_rand(FaultFoundCode::getDropDownValues(false));
    $failureFaultInduced = array_rand(FaultInducedCode::getDropDownValues(false));
    $hardwareSoftwareFailure = array_rand(HardwareSoftwareFailureCode::getDropDownValues(false));
    $faultConfirmsReasonForRemoval = array_rand(FaultConfirmsReasonForRemovalCode::getDropDownValues(false));
    $faultConfirmsAircraftMessage = array_rand(FaultConfirmsAircraftMessageCode::getDropDownValues(false));
    $faultConfirmsAircraftPartBiteMessageCode = array_rand(FaultConfirmsAircraftPartBiteMessageCode::getDropDownValues(false));
    
    $cageCodes = CageCode::getPermittedValues();
    $cageCode = $cageCodes[array_rand($cageCodes)];
    
    if ($failureFaultFound != 'FT') {
        if ($supplierRemovalType == 'M') {
            $failureFaultFound = 'NA';
            
        } else {
            $failureFaultFound = 'NT';
        }
        
        $failureFaultInduced = 'NA';
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $failureFaultInduced = $faker->boolean() ? 'NI' : 'IN';
    }
    
    if ($failureFaultInduced != 'NI') {
        $hardwareSoftwareFailure = 'NA';
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    } else {
        $hardwareSoftwareFailure = $faker->boolean() ? 'HW' : 'SW';
    }
    
    if ($hardwareSoftwareFailure != 'NA') {
        if (in_array($supplierRemovalType, ['S','M'])) {
            $faultConfirmsReasonForRemoval = 'NA';
            $faultConfirmsAircraftMessage = 'NA';
            $faultConfirmsAircraftPartBiteMessageCode = 'NA';
        } else {
            $faultConfirmsReasonForRemoval = $faker->boolean() ? 'CR' : 'NC';
        }
    } else {
        $faultConfirmsReasonForRemoval = 'NA';
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    if ($faultConfirmsReasonForRemoval == 'NC') {
        $faultConfirmsAircraftMessage = 'NA';
        $faultConfirmsAircraftPartBiteMessageCode = 'NA';
    }
    
    $mfn = NULL;
    
    if (in_array($cageCode, ['ZZZZZ', 'zzzzz'])) {
        $mfn = trim(substr($faker->company, 0, 55));
    } else if ($faker->boolean()) {
        $mfn = trim(substr($faker->company, 0, 55));
    }
        
    return [
        'SFI' => (string) $faker->unique()->numberBetween(100000, 9999999999),
        'MRD' => $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now'),
        'MFR' => $cageCode,
        'MPN' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'SER' => Str::random(rand(7,15)),
        'RRC' => $supplierRemovalType,
        'FFC' => $failureFaultFound,
        'FFI' => $failureFaultInduced,
        'FCR' => $faultConfirmsReasonForRemoval,
        'FAC' => $faultConfirmsAircraftMessage,
        'FBC' => $faultConfirmsAircraftPartBiteMessageCode,
        'FHS' => $hardwareSoftwareFailure,
        'MFN' => $mfn,
        'PNR' => function(){
            return UtasCode::inRandomOrder()->first()->MATNR;
        },
        'OPN' => $faker->boolean() ? Str::random($rand16and32) : NULL,
        'USN' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'RET' => $faker->boolean() ? Str::random($rand1and64) : NULL,
        'CIC' => $faker->boolean() ? Str::random($rand3and5) : NULL,
        'CPO' => $faker->boolean() ? Str::random($rand1and11) : NULL,
        'PSN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'WON' => $faker->boolean() ? Str::random($rand1and20) : NULL,
        'MRN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'CTN' => $faker->boolean() ? Str::random($rand4and15) : NULL,
        'BOX' => $faker->boolean() ? Str::random($rand1and10) : NULL,
        'ASN' => $faker->boolean() ? Str::random($rand1and32) : NULL,
        'UCN' => $faker->boolean() ? Str::random($rand1and15) : NULL,
        'SPL' => $faker->boolean() ? Str::random(5) : NULL,
        'UST' => $faker->boolean() ? Str::random($rand6and20) : NULL,
        'PDT' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'PML' => trim(substr($faker->optional()->sentence(), 0, 100)),
        'SFC' => $faker->boolean() ? Str::random($rand1and10) : NULL,
        'RSI' => $faker->boolean() ? Str::random($rand1and50) : NULL,
        'RLN' => $faker->boolean() ? Str::random($rand1and25) : NULL,
        'INT' => $faker->optional()->paragraph(),
        'REM' => NULL
    ];
});