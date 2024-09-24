<?php

use App\HDR_Segment;
use App\Spec2kReport;
use Carbon\Carbon;
use Faker\Generator as Faker;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;

$factory->define(Spec2kReport::class, function (Faker $faker) {
    $HDR_Segment = (array)factory(App\HDR_Segment::class)->make()->getAttributes();
    $HDR_Segment['RDT'] = $HDR_Segment['RDT'] ? new \MongoDB\BSON\UTCDateTime(strtotime($HDR_Segment['RDT'])*1000) : NULL;
    $HDR_Segment['RSD'] = $HDR_Segment['RSD'] ? new \MongoDB\BSON\UTCDateTime(strtotime($HDR_Segment['RSD'])*1000) : NULL;
    
    $RCS_Segment = (array) factory(RCS_Segment::class)->make()->getAttributes();
    $RCS_Segment['MRD'] = $RCS_Segment['MRD'] ? new \MongoDB\BSON\UTCDateTime(strtotime($RCS_Segment['MRD'])*1000) : NULL;
    
    $SUS_Segment = (array) factory(SUS_Segment::class)->make()->getAttributes();
    $SUS_Segment['SHD'] = $SUS_Segment['SHD'] ? new \MongoDB\BSON\UTCDateTime(strtotime($SUS_Segment['SHD'])*1000) : NULL;
    
    $RLS_Segment = (array) factory(RLS_Segment::class)->make()->getAttributes();
    $RLS_Segment['RED'] = $RLS_Segment['RED'] ? new \MongoDB\BSON\UTCDateTime(strtotime($RLS_Segment['RED'])*1000) : NULL;
    $RLS_Segment['DOI'] = $RLS_Segment['DOI'] ? new \MongoDB\BSON\UTCDateTime(strtotime($RLS_Segment['DOI'])*1000) : NULL;
    
    $numberOfPieceParts = mt_rand(0, 7);
    $piecePartDetails = [];
    
    if (count($numberOfPieceParts)) {
        while($numberOfPieceParts > 0) {
            
            $WPS_Segment = (array) factory(WPS_Segment::class)->make(['SFI' => $RCS_Segment['SFI']])->getAttributes();
            $WPS_Segment['MRD'] = $WPS_Segment['MRD'] ? new \MongoDB\BSON\UTCDateTime(strtotime($WPS_Segment['MRD'])*1000) : NULL;
            
            $piecePartDetails[] = [
                'WPS_Segment' => $WPS_Segment,
                'NHS_Segment' => $faker->boolean() ? factory(NHS_Segment::class)->make()->getAttributes() : NULL,
                'RPS_Segment' => $faker->boolean() ? factory(SPT_Segment::class)->make()->getAttributes() : NULL
            ];
            
            $numberOfPieceParts--;
        }
    }
    
    $atts['ShopFindings']['HDR_Segment'] = $HDR_Segment;
    
    $atts['ShopFindings']['ShopFindingsDetails'] = [
        'HDR_Segment' => $HDR_Segment,
        'AID_Segment' => $faker->boolean() ? (array) factory(AID_Segment::class)->make()->getAttributes() : NULL,
        'EID_Segment' => $faker->boolean() ? (array) factory(EID_Segment::class)->make()->getAttributes() : NULL,
        'API_Segment' => $faker->boolean() ? (array) factory(API_Segment::class)->make()->getAttributes() : NULL,
        'RCS_Segment' => $RCS_Segment,
        'SAS_Segment' => (array) factory(SAS_Segment::class)->make()->getAttributes(),
        'SUS_Segment' => $faker->boolean() ? $SUS_Segment : NULL,
        'RLS_Segment' => $faker->boolean() ? $RLS_Segment : NULL,
        'LNK_Segment' => $faker->boolean() ? (array) factory(LNK_Segment::class)->make()->getAttributes() : NULL,
        'ATT_Segment' => $faker->boolean() ? (array) factory(ATT_Segment::class)->make()->getAttributes() : NULL,
        'SPT_Segment' => $faker->boolean() ? (array) factory(SPT_Segment::class)->make()->getAttributes() : NULL,
    ];
    
    $atts['PieceParts']['HDR_Segment'] = $HDR_Segment;
    
    if (count($piecePartDetails)) {
        foreach ($piecePartDetails as $k => $val) {
            $atts['PieceParts']['PiecePartDetails'][$k] = $val;
        }
    }
    
    return $atts;
});