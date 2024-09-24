<?php

use App\User;
use App\Location;
use App\HDR_Segment;
use Faker\Generator as Faker;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\Misc_Segment;
use App\Notification;

$factory->define(Notification::class, function (Faker $faker) {
    
    // Mandatory.
    $HDR_Segment = factory(HDR_Segment::class)->raw();
    $RCS_Segment = factory(RCS_Segment::class)->raw();
    $SAS_Segment = factory(SAS_Segment::class)->raw();
    
    // Optional.
    $AID_Segment = factory(AID_Segment::class)->raw();
    $API_Segment = factory(API_Segment::class)->raw();
    $ATT_Segment = factory(ATT_Segment::class)->raw();
    $EID_Segment = factory(EID_Segment::class)->raw();
    $LNK_Segment = factory(LNK_Segment::class)->raw();
    $RLS_Segment = factory(RLS_Segment::class)->raw();
    $SPT_Segment = factory(SPT_Segment::class)->raw();
    $SUS_Segment = factory(SUS_Segment::class)->raw();
    
    $attributes = [
        'id' => $RCS_Segment['SFI'],
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'hdrCHG' => $HDR_Segment['CHG'],
        'hdrROC' => $HDR_Segment['ROC'],
        'hdrRDT' => $HDR_Segment['RDT'],
        'hdrRSD' => $HDR_Segment['RSD'],
        'hdrOPR' => $HDR_Segment['OPR'],
        'hdrRON' => $HDR_Segment['RON'],
        'hdrWHO' => $HDR_Segment['WHO'],
        
        'rcsSFI' => $RCS_Segment['SFI'],
        'rcsMRD' => $RCS_Segment['MRD'],
        'rcsMFR' => $RCS_Segment['MFR'],
        'rcsMPN' => $RCS_Segment['MPN'],
        'rcsSER' => $RCS_Segment['SER'],
        'rcsRRC' => $RCS_Segment['RRC'],
        'rcsFFC' => $RCS_Segment['FFC'],
        'rcsFFI' => $RCS_Segment['FFI'],
        'rcsFCR' => $RCS_Segment['FCR'],
        'rcsFAC' => $RCS_Segment['FAC'],
        'rcsFBC' => $RCS_Segment['FBC'],
        'rcsFHS' => $RCS_Segment['FHS'],
        'rcsMFN' => $RCS_Segment['MFN'],
        'rcsPNR' => $RCS_Segment['PNR'],
        'rcsOPN' => $RCS_Segment['OPN'],
        'rcsUSN' => $RCS_Segment['USN'],
        'rcsRET' => $RCS_Segment['RET'],
        'rcsCIC' => $RCS_Segment['CIC'],
        'rcsCPO' => $RCS_Segment['CPO'],
        'rcsPSN' => $RCS_Segment['PSN'],
        'rcsWON' => $RCS_Segment['WON'],
        'rcsMRN' => $RCS_Segment['MRN'],
        'rcsCTN' => $RCS_Segment['CTN'],
        'rcsBOX' => $RCS_Segment['BOX'],
        'rcsASN' => $RCS_Segment['ASN'],
        'rcsUCN' => $RCS_Segment['UCN'],
        'rcsSPL' => $RCS_Segment['SPL'],
        'rcsUST' => $RCS_Segment['UST'],
        'rcsPDT' => $RCS_Segment['PDT'],
        'rcsPML' => $RCS_Segment['PML'],
        'rcsSFC' => $RCS_Segment['SFC'],
        'rcsRSI' => $RCS_Segment['RSI'],
        'rcsRLN' => $RCS_Segment['RLN'],
        'rcsINT' => $RCS_Segment['INT'],
        'rcsREM' => $RCS_Segment['REM'],
        
        'sasINT' => $SAS_Segment['INT'],
        'sasSHL' => $SAS_Segment['SHL'],
        'sasRFI' => $SAS_Segment['RFI'],
        'sasMAT' => $SAS_Segment['MAT'],
        'sasSAC' => $SAS_Segment['SAC'],
        'sasSDI' => $SAS_Segment['SDI'],
        'sasPSC' => $SAS_Segment['PSC'],
        'sasREM' => $SAS_Segment['REM'],
        
        'planner_group' => function(){
            return User::inRandomOrder()->first()->planner_group;
        },
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL
    ];
    
    if ($faker->boolean) {
        $AID_Segment = factory(AID_Segment::class)->raw();
        
        $attributes['aidMFR'] = $AID_Segment['MFR'];
        $attributes['aidAMC'] = $AID_Segment['AMC'];
        $attributes['aidMFN'] = $AID_Segment['MFN'];
        $attributes['aidASE'] = $AID_Segment['ASE'];
        $attributes['aidAIN'] = $AID_Segment['AIN'];
        $attributes['aidREG'] = $AID_Segment['REG'];
        $attributes['aidOIN'] = $AID_Segment['OIN'];
        $attributes['aidCTH'] = $AID_Segment['CTH'];
        $attributes['aidCTY'] = $AID_Segment['CTY'];
    }
    
    if ($faker->boolean) {
        $API_Segment = factory(API_Segment::class)->raw();
        
        $attributes['apiAET'] = $API_Segment['AET'];
        $attributes['apiEMS'] = $API_Segment['EMS'];
        $attributes['apiAEM'] = $API_Segment['AEM'];
        $attributes['apiMFR'] = $API_Segment['MFR'];
        $attributes['apiATH'] = $API_Segment['ATH'];
        $attributes['apiATC'] = $API_Segment['ATC'];
    }
    
    if ($faker->boolean) {
        $ATT_Segment = factory(ATT_Segment::class)->raw();
        
        $attributes['attTRF'] = $ATT_Segment['TRF'];
        $attributes['attOTT'] = $ATT_Segment['OTT'];
        $attributes['attOPC'] = $ATT_Segment['OPC'];
        $attributes['attODT'] = $ATT_Segment['ODT'];
    }
    
    if ($faker->boolean) {
        $EID_Segment = factory(EID_Segment::class)->raw();
        
        $attributes['eidAET'] = $EID_Segment['AET'];
        $attributes['eidEPC'] = $EID_Segment['EPC'];
        $attributes['eidAEM'] = $EID_Segment['AEM'];
        $attributes['eidEMS'] = $EID_Segment['EMS'];
        $attributes['eidMFR'] = $EID_Segment['MFR'];
        $attributes['eidETH'] = $EID_Segment['ETH'];
        $attributes['eidETC'] = $EID_Segment['ETC'];
    }
    
    if ($faker->boolean) {
        $LNK_Segment = factory(LNK_Segment::class)->raw();
        
        $attributes['lnkRTI'] = $LNK_Segment['RTI'];
    }
    
    if ($faker->boolean) {
        $RLS_Segment = factory(RLS_Segment::class)->raw();
        
        $attributes['rlsMFR'] = $RLS_Segment['MFR'];
        $attributes['rlsMPN'] = $RLS_Segment['MPN'];
        $attributes['rlsSER'] = $RLS_Segment['SER'];
        $attributes['rlsRED'] = $RLS_Segment['RED'];
        $attributes['rlsTTY'] = $RLS_Segment['TTY'];
        $attributes['rlsRET'] = $RLS_Segment['RET'];
        $attributes['rlsDOI'] = $RLS_Segment['DOI'];
        $attributes['rlsMFN'] = $RLS_Segment['MFN'];
        $attributes['rlsPNR'] = $RLS_Segment['PNR'];
        $attributes['rlsOPN'] = $RLS_Segment['OPN'];
        $attributes['rlsUSN'] = $RLS_Segment['USN'];
        $attributes['rlsRMT'] = $RLS_Segment['RMT'];
        $attributes['rlsAPT'] = $RLS_Segment['APT'];
        $attributes['rlsCPI'] = $RLS_Segment['CPI'];
        $attributes['rlsCPT'] = $RLS_Segment['CPT'];
        $attributes['rlsPDT'] = $RLS_Segment['PDT'];
        $attributes['rlsPML'] = $RLS_Segment['PML'];
        $attributes['rlsASN'] = $RLS_Segment['ASN'];
        $attributes['rlsUCN'] = $RLS_Segment['UCN'];
        $attributes['rlsSPL'] = $RLS_Segment['SPL'];
        $attributes['rlsUST'] = $RLS_Segment['UST'];
        $attributes['rlsRFR'] = $RLS_Segment['RFR'];
    }
    
    if ($faker->boolean) {
        $SPT_Segment = factory(SPT_Segment::class)->raw();
        
        $attributes['sptMAH'] = $SPT_Segment['MAH'];
        $attributes['sptFLW'] = $SPT_Segment['FLW'];
        $attributes['sptMST'] = $SPT_Segment['MST'];
    }
    
    if ($faker->boolean) {
        $SUS_Segment = factory(SUS_Segment::class)->raw();
        
        $attributes['susSHD'] = $SUS_Segment['SHD'];
        $attributes['susMFR'] = $SUS_Segment['MFR'];
        $attributes['susMPN'] = $SUS_Segment['MPN'];
        $attributes['susSER'] = $SUS_Segment['SER'];
        $attributes['susMFN'] = $SUS_Segment['MFN'];
        $attributes['susPDT'] = $SUS_Segment['PDT'];
        $attributes['susPNR'] = $SUS_Segment['PNR'];
        $attributes['susOPN'] = $SUS_Segment['OPN'];
        $attributes['susUSN'] = $SUS_Segment['USN'];
        $attributes['susASN'] = $SUS_Segment['ASN'];
        $attributes['susUCN'] = $SUS_Segment['UCN'];
        $attributes['susSPL'] = $SUS_Segment['SPL'];
        $attributes['susUST'] = $SUS_Segment['UST'];
        $attributes['susPML'] = $SUS_Segment['PML'];
        $attributes['susPSC'] = $SUS_Segment['PSC'];
    }
    
    return $attributes;
});

$factory->state(Notification::class, 'all_segments', function (Faker $faker) {
    
    $HDR_Segment = factory(HDR_Segment::class)->raw();
    $AID_Segment = factory(AID_Segment::class)->raw();
    $API_Segment = factory(API_Segment::class)->raw();
    $ATT_Segment = factory(ATT_Segment::class)->raw();
    $EID_Segment = factory(EID_Segment::class)->raw();
    $LNK_Segment = factory(LNK_Segment::class)->raw();
    $RCS_Segment = factory(RCS_Segment::class)->raw();
    $RLS_Segment = factory(RLS_Segment::class)->raw();
    $SAS_Segment = factory(SAS_Segment::class)->raw();
    $SPT_Segment = factory(SPT_Segment::class)->raw();
    $SUS_Segment = factory(SUS_Segment::class)->raw();
    
    return [
        'id' => $RCS_Segment['SFI'],
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'hdrCHG' => $HDR_Segment['CHG'],
        'hdrROC' => $HDR_Segment['ROC'],
        'hdrRDT' => $HDR_Segment['RDT'],
        'hdrRSD' => $HDR_Segment['RSD'],
        'hdrOPR' => $HDR_Segment['OPR'],
        'hdrRON' => $HDR_Segment['RON'],
        'hdrWHO' => $HDR_Segment['WHO'],
        
        'aidMFR' => $AID_Segment['MFR'],
        'aidAMC' => $AID_Segment['AMC'],
        'aidMFN' => $AID_Segment['MFN'],
        'aidASE' => $AID_Segment['ASE'],
        'aidAIN' => $AID_Segment['AIN'],
        'aidREG' => $AID_Segment['REG'],
        'aidOIN' => $AID_Segment['OIN'],
        'aidCTH' => $AID_Segment['CTH'],
        'aidCTY' => $AID_Segment['CTY'],
        
        'eidAET' => $EID_Segment['AET'],
        'eidEPC' => $EID_Segment['EPC'],
        'eidAEM' => $EID_Segment['AEM'],
        'eidEMS' => $EID_Segment['EMS'],
        'eidMFR' => $EID_Segment['MFR'],
        'eidETH' => $EID_Segment['ETH'],
        'eidETC' => $EID_Segment['ETC'],
        
        'apiAET' => $API_Segment['AET'],
        'apiEMS' => $API_Segment['EMS'],
        'apiAEM' => $API_Segment['AEM'],
        'apiMFR' => $API_Segment['MFR'],
        'apiATH' => $API_Segment['ATH'],
        'apiATC' => $API_Segment['ATC'],
        
        'rcsSFI' => $RCS_Segment['SFI'],
        'rcsMRD' => $RCS_Segment['MRD'],
        'rcsMFR' => $RCS_Segment['MFR'],
        'rcsMPN' => $RCS_Segment['MPN'],
        'rcsSER' => $RCS_Segment['SER'],
        'rcsRRC' => $RCS_Segment['RRC'],
        'rcsFFC' => $RCS_Segment['FFC'],
        'rcsFFI' => $RCS_Segment['FFI'],
        'rcsFCR' => $RCS_Segment['FCR'],
        'rcsFAC' => $RCS_Segment['FAC'],
        'rcsFBC' => $RCS_Segment['FBC'],
        'rcsFHS' => $RCS_Segment['FHS'],
        'rcsMFN' => $RCS_Segment['MFN'],
        'rcsPNR' => $RCS_Segment['PNR'],
        'rcsOPN' => $RCS_Segment['OPN'],
        'rcsUSN' => $RCS_Segment['USN'],
        'rcsRET' => $RCS_Segment['RET'],
        'rcsCIC' => $RCS_Segment['CIC'],
        'rcsCPO' => $RCS_Segment['CPO'],
        'rcsPSN' => $RCS_Segment['PSN'],
        'rcsWON' => $RCS_Segment['WON'],
        'rcsMRN' => $RCS_Segment['MRN'],
        'rcsCTN' => $RCS_Segment['CTN'],
        'rcsBOX' => $RCS_Segment['BOX'],
        'rcsASN' => $RCS_Segment['ASN'],
        'rcsUCN' => $RCS_Segment['UCN'],
        'rcsSPL' => $RCS_Segment['SPL'],
        'rcsUST' => $RCS_Segment['UST'],
        'rcsPDT' => $RCS_Segment['PDT'],
        'rcsPML' => $RCS_Segment['PML'],
        'rcsSFC' => $RCS_Segment['SFC'],
        'rcsRSI' => $RCS_Segment['RSI'],
        'rcsRLN' => $RCS_Segment['RLN'],
        'rcsINT' => $RCS_Segment['INT'],
        'rcsREM' => $RCS_Segment['REM'],
        
        'sasINT' => $SAS_Segment['INT'],
        'sasSHL' => $SAS_Segment['SHL'],
        'sasRFI' => $SAS_Segment['RFI'],
        'sasMAT' => $SAS_Segment['MAT'],
        'sasSAC' => $SAS_Segment['SAC'],
        'sasSDI' => $SAS_Segment['SDI'],
        'sasPSC' => $SAS_Segment['PSC'],
        'sasREM' => $SAS_Segment['REM'],
        
        'susSHD' => $SUS_Segment['SHD'],
        'susMFR' => $SUS_Segment['MFR'],
        'susMPN' => $SUS_Segment['MPN'],
        'susSER' => $SUS_Segment['SER'],
        'susMFN' => $SUS_Segment['MFN'],
        'susPDT' => $SUS_Segment['PDT'],
        'susPNR' => $SUS_Segment['PNR'],
        'susOPN' => $SUS_Segment['OPN'],
        'susUSN' => $SUS_Segment['USN'],
        'susASN' => $SUS_Segment['ASN'],
        'susUCN' => $SUS_Segment['UCN'],
        'susSPL' => $SUS_Segment['SPL'],
        'susUST' => $SUS_Segment['UST'],
        'susPML' => $SUS_Segment['PML'],
        'susPSC' => $SUS_Segment['PSC'],
        
        'rlsMFR' => $RLS_Segment['MFR'],
        'rlsMPN' => $RLS_Segment['MPN'],
        'rlsSER' => $RLS_Segment['SER'],
        'rlsRED' => $RLS_Segment['RED'],
        'rlsTTY' => $RLS_Segment['TTY'],
        'rlsRET' => $RLS_Segment['RET'],
        'rlsDOI' => $RLS_Segment['DOI'],
        'rlsMFN' => $RLS_Segment['MFN'],
        'rlsPNR' => $RLS_Segment['PNR'],
        'rlsOPN' => $RLS_Segment['OPN'],
        'rlsUSN' => $RLS_Segment['USN'],
        'rlsRMT' => $RLS_Segment['RMT'],
        'rlsAPT' => $RLS_Segment['APT'],
        'rlsCPI' => $RLS_Segment['CPI'],
        'rlsCPT' => $RLS_Segment['CPT'],
        'rlsPDT' => $RLS_Segment['PDT'],
        'rlsPML' => $RLS_Segment['PML'],
        'rlsASN' => $RLS_Segment['ASN'],
        'rlsUCN' => $RLS_Segment['UCN'],
        'rlsSPL' => $RLS_Segment['SPL'],
        'rlsUST' => $RLS_Segment['UST'],
        'rlsRFR' => $RLS_Segment['RFR'],
        
        'lnkRTI' => $LNK_Segment['RTI'],
        
        'attTRF' => $ATT_Segment['TRF'],
        'attOTT' => $ATT_Segment['OTT'],
        'attOPC' => $ATT_Segment['OPC'],
        'attODT' => $ATT_Segment['ODT'],
        
        'sptMAH' => $SPT_Segment['MAH'],
        'sptFLW' => $SPT_Segment['FLW'],
        'sptMST' => $SPT_Segment['MST'],
        
        'planner_group' => function(){
            return User::inRandomOrder()->first()->planner_group;
        },
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL
    ];
});

$factory->state(Notification::class, 'all_segments_real_arcraft_data', function (Faker $faker) {
    
    $HDR_Segment = factory(HDR_Segment::class)->raw();
    $AID_Segment = factory(AID_Segment::class)->states('with_real_aircraft_data')->raw();
    $API_Segment = factory(API_Segment::class)->raw();
    $ATT_Segment = factory(ATT_Segment::class)->raw();
    $EID_Segment = factory(EID_Segment::class)->raw();
    $LNK_Segment = factory(LNK_Segment::class)->raw();
    $RCS_Segment = factory(RCS_Segment::class)->raw();
    $RLS_Segment = factory(RLS_Segment::class)->raw();
    $SAS_Segment = factory(SAS_Segment::class)->raw();
    $SPT_Segment = factory(SPT_Segment::class)->raw();
    $SUS_Segment = factory(SUS_Segment::class)->raw();
    
    return [
        'id' => $RCS_Segment['SFI'],
        'plant_code' => function(){
            return Location::inRandomOrder()->first()->plant_code;
        },
        'hdrCHG' => $HDR_Segment['CHG'],
        'hdrROC' => $HDR_Segment['ROC'],
        'hdrRDT' => $HDR_Segment['RDT'],
        'hdrRSD' => $HDR_Segment['RSD'],
        'hdrOPR' => $HDR_Segment['OPR'],
        'hdrRON' => $HDR_Segment['RON'],
        'hdrWHO' => $HDR_Segment['WHO'],
        
        'aidMFR' => $AID_Segment['MFR'],
        'aidAMC' => $AID_Segment['AMC'],
        'aidMFN' => $AID_Segment['MFN'],
        'aidASE' => $AID_Segment['ASE'],
        'aidAIN' => $AID_Segment['AIN'],
        'aidREG' => $AID_Segment['REG'],
        'aidOIN' => $AID_Segment['OIN'],
        'aidCTH' => $AID_Segment['CTH'],
        'aidCTY' => $AID_Segment['CTY'],
        
        'eidAET' => $EID_Segment['AET'],
        'eidEPC' => $EID_Segment['EPC'],
        'eidAEM' => $EID_Segment['AEM'],
        'eidEMS' => $EID_Segment['EMS'],
        'eidMFR' => $EID_Segment['MFR'],
        'eidETH' => $EID_Segment['ETH'],
        'eidETC' => $EID_Segment['ETC'],
        
        'apiAET' => $API_Segment['AET'],
        'apiEMS' => $API_Segment['EMS'],
        'apiAEM' => $API_Segment['AEM'],
        'apiMFR' => $API_Segment['MFR'],
        'apiATH' => $API_Segment['ATH'],
        'apiATC' => $API_Segment['ATC'],
        
        'rcsSFI' => $RCS_Segment['SFI'],
        'rcsMRD' => $RCS_Segment['MRD'],
        'rcsMFR' => $RCS_Segment['MFR'],
        'rcsMPN' => $RCS_Segment['MPN'],
        'rcsSER' => $RCS_Segment['SER'],
        'rcsRRC' => $RCS_Segment['RRC'],
        'rcsFFC' => $RCS_Segment['FFC'],
        'rcsFFI' => $RCS_Segment['FFI'],
        'rcsFCR' => $RCS_Segment['FCR'],
        'rcsFAC' => $RCS_Segment['FAC'],
        'rcsFBC' => $RCS_Segment['FBC'],
        'rcsFHS' => $RCS_Segment['FHS'],
        'rcsMFN' => $RCS_Segment['MFN'],
        'rcsPNR' => $RCS_Segment['PNR'],
        'rcsOPN' => $RCS_Segment['OPN'],
        'rcsUSN' => $RCS_Segment['USN'],
        'rcsRET' => $RCS_Segment['RET'],
        'rcsCIC' => $RCS_Segment['CIC'],
        'rcsCPO' => $RCS_Segment['CPO'],
        'rcsPSN' => $RCS_Segment['PSN'],
        'rcsWON' => $RCS_Segment['WON'],
        'rcsMRN' => $RCS_Segment['MRN'],
        'rcsCTN' => $RCS_Segment['CTN'],
        'rcsBOX' => $RCS_Segment['BOX'],
        'rcsASN' => $RCS_Segment['ASN'],
        'rcsUCN' => $RCS_Segment['UCN'],
        'rcsSPL' => $RCS_Segment['SPL'],
        'rcsUST' => $RCS_Segment['UST'],
        'rcsPDT' => $RCS_Segment['PDT'],
        'rcsPML' => $RCS_Segment['PML'],
        'rcsSFC' => $RCS_Segment['SFC'],
        'rcsRSI' => $RCS_Segment['RSI'],
        'rcsRLN' => $RCS_Segment['RLN'],
        'rcsINT' => $RCS_Segment['INT'],
        'rcsREM' => $RCS_Segment['REM'],
        
        'sasINT' => $SAS_Segment['INT'],
        'sasSHL' => $SAS_Segment['SHL'],
        'sasRFI' => $SAS_Segment['RFI'],
        'sasMAT' => $SAS_Segment['MAT'],
        'sasSAC' => $SAS_Segment['SAC'],
        'sasSDI' => $SAS_Segment['SDI'],
        'sasPSC' => $SAS_Segment['PSC'],
        'sasREM' => $SAS_Segment['REM'],
        
        'susSHD' => $SUS_Segment['SHD'],
        'susMFR' => $SUS_Segment['MFR'],
        'susMPN' => $SUS_Segment['MPN'],
        'susSER' => $SUS_Segment['SER'],
        'susMFN' => $SUS_Segment['MFN'],
        'susPDT' => $SUS_Segment['PDT'],
        'susPNR' => $SUS_Segment['PNR'],
        'susOPN' => $SUS_Segment['OPN'],
        'susUSN' => $SUS_Segment['USN'],
        'susASN' => $SUS_Segment['ASN'],
        'susUCN' => $SUS_Segment['UCN'],
        'susSPL' => $SUS_Segment['SPL'],
        'susUST' => $SUS_Segment['UST'],
        'susPML' => $SUS_Segment['PML'],
        'susPSC' => $SUS_Segment['PSC'],
        
        'rlsMFR' => $RLS_Segment['MFR'],
        'rlsMPN' => $RLS_Segment['MPN'],
        'rlsSER' => $RLS_Segment['SER'],
        'rlsRED' => $RLS_Segment['RED'],
        'rlsTTY' => $RLS_Segment['TTY'],
        'rlsRET' => $RLS_Segment['RET'],
        'rlsDOI' => $RLS_Segment['DOI'],
        'rlsMFN' => $RLS_Segment['MFN'],
        'rlsPNR' => $RLS_Segment['PNR'],
        'rlsOPN' => $RLS_Segment['OPN'],
        'rlsUSN' => $RLS_Segment['USN'],
        'rlsRMT' => $RLS_Segment['RMT'],
        'rlsAPT' => $RLS_Segment['APT'],
        'rlsCPI' => $RLS_Segment['CPI'],
        'rlsCPT' => $RLS_Segment['CPT'],
        'rlsPDT' => $RLS_Segment['PDT'],
        'rlsPML' => $RLS_Segment['PML'],
        'rlsASN' => $RLS_Segment['ASN'],
        'rlsUCN' => $RLS_Segment['UCN'],
        'rlsSPL' => $RLS_Segment['SPL'],
        'rlsUST' => $RLS_Segment['UST'],
        'rlsRFR' => $RLS_Segment['RFR'],
        
        'lnkRTI' => $LNK_Segment['RTI'],
        
        'attTRF' => $ATT_Segment['TRF'],
        'attOTT' => $ATT_Segment['OTT'],
        'attOPC' => $ATT_Segment['OPC'],
        'attODT' => $ATT_Segment['ODT'],
        
        'sptMAH' => $SPT_Segment['MAH'],
        'sptFLW' => $SPT_Segment['FLW'],
        'sptMST' => $SPT_Segment['MST'],
        
        'planner_group' => function(){
            return User::inRandomOrder()->first()->planner_group;
        },
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL
    ];
});

$factory->state(Notification::class, 'stub', function (Faker $faker) {
    
    return [
        'id' => NULL,
        'plant_code' => NULL,
        'hdrCHG' => NULL,
        'hdrROC' => NULL,
        'hdrRDT' => NULL,
        'hdrRSD' => NULL,
        'hdrOPR' => NULL,
        'hdrRON' => NULL,
        'hdrWHO' => NULL,
        
        'aidMFR' => NULL,
        'aidAMC' => NULL,
        'aidMFN' => NULL,
        'aidASE' => NULL,
        'aidAIN' => NULL,
        'aidREG' => NULL,
        'aidOIN' => NULL,
        'aidCTH' => NULL,
        'aidCTY' => NULL,
        
        'eidAET' => NULL,
        'eidEPC' => NULL,
        'eidAEM' => NULL,
        'eidEMS' => NULL,
        'eidMFR' => NULL,
        'eidETH' => NULL,
        'eidETC' => NULL,
        
        'apiAET' => NULL,
        'apiEMS' => NULL,
        'apiAEM' => NULL,
        'apiMFR' => NULL,
        'apiATH' => NULL,
        'apiATC' => NULL,
        
        'rcsSFI' => NULL,
        'rcsMRD' => NULL,
        'rcsMFR' => NULL,
        'rcsMPN' => NULL,
        'rcsSER' => NULL,
        'rcsRRC' => NULL,
        'rcsFFC' => NULL,
        'rcsFFI' => NULL,
        'rcsFCR' => NULL,
        'rcsFAC' => NULL,
        'rcsFBC' => NULL,
        'rcsFHS' => NULL,
        'rcsMFN' => NULL,
        'rcsPNR' => NULL,
        'rcsOPN' => NULL,
        'rcsUSN' => NULL,
        'rcsRET' => NULL,
        'rcsCIC' => NULL,
        'rcsCPO' => NULL,
        'rcsPSN' => NULL,
        'rcsWON' => NULL,
        'rcsMRN' => NULL,
        'rcsCTN' => NULL,
        'rcsBOX' => NULL,
        'rcsASN' => NULL,
        'rcsUCN' => NULL,
        'rcsSPL' => NULL,
        'rcsUST' => NULL,
        'rcsPDT' => NULL,
        'rcsPML' => NULL,
        'rcsSFC' => NULL,
        'rcsRSI' => NULL,
        'rcsRLN' => NULL,
        'rcsINT' => NULL,
        'rcsREM' => NULL,
        
        'sasINT' => NULL,
        'sasSHL' => NULL,
        'sasRFI' => NULL,
        'sasMAT' => NULL,
        'sasSAC' => NULL,
        'sasSDI' => NULL,
        'sasPSC' => NULL,
        'sasREM' => NULL,
        
        'susSHD' => NULL,
        'susMFR' => NULL,
        'susMPN' => NULL,
        'susSER' => NULL,
        'susMFN' => NULL,
        'susPDT' => NULL,
        'susPNR' => NULL,
        'susOPN' => NULL,
        'susUSN' => NULL,
        'susASN' => NULL,
        'susUCN' => NULL,
        'susSPL' => NULL,
        'susUST' => NULL,
        'susPML' => NULL,
        'susPSC' => NULL,
        
        'rlsMFR' => NULL,
        'rlsMPN' => NULL,
        'rlsSER' => NULL,
        'rlsRED' => NULL,
        'rlsTTY' => NULL,
        'rlsRET' => NULL,
        'rlsDOI' => NULL,
        'rlsMFN' => NULL,
        'rlsPNR' => NULL,
        'rlsOPN' => NULL,
        'rlsUSN' => NULL,
        'rlsRMT' => NULL,
        'rlsAPT' => NULL,
        'rlsCPI' => NULL,
        'rlsCPT' => NULL,
        'rlsPDT' => NULL,
        'rlsPML' => NULL,
        'rlsASN' => NULL,
        'rlsUCN' => NULL,
        'rlsSPL' => NULL,
        'rlsUST' => NULL,
        'rlsRFR' => NULL,
        
        'lnkRTI' => NULL,
        
        'attTRF' => NULL,
        'attOTT' => NULL,
        'attOPC' => NULL,
        'attODT' => NULL,
        
        'sptMAH' => NULL,
        'sptFLW' => NULL,
        'sptMST' => NULL,
        
        'planner_group' => NULL,
        'status' => 'in_progress',
        'standby_at' => NULL,
        'subcontracted_at' => NULL,
        'scrapped_at' => NULL,
        'shipped_at' => NULL
    ];
});