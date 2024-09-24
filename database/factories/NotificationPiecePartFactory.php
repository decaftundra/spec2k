<?php

use Faker\Generator as Faker;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\PieceParts\WPS_Segment;
use Illuminate\Support\Facades\DB;
use App\Notification;
use App\NotificationPiecePart;

$factory->define(NotificationPiecePart::class, function (Faker $faker) {
    
    $WPS_Segment = factory(WPS_Segment::class)->raw();
    
    $attributes = [
        'id' => $WPS_Segment['PPI'],
        'notification_id' => function(){
            return factory(Notification::class)->create()->id;
        },
        'wpsSFI' => function (){
            return DB::getPdo()->lastInsertId();
        },
        'wpsPPI' => $WPS_Segment['PPI'],
        'wpsPFC' => $WPS_Segment['PFC'],
        'wpsMFR' => $WPS_Segment['MFR'],
        'wpsMFN' => $WPS_Segment['MFN'],
        'wpsMPN' => $WPS_Segment['MPN'],
        'wpsSER' => $WPS_Segment['SER'],
        'wpsFDE' => $WPS_Segment['FDE'],
        'wpsPNR' => $WPS_Segment['PNR'],
        'wpsOPN' => $WPS_Segment['OPN'],
        'wpsUSN' => $WPS_Segment['USN'],
        'wpsPDT' => $WPS_Segment['PDT'],
        'wpsGEL' => $WPS_Segment['GEL'],
        'wpsMRD' => $WPS_Segment['MRD'],
        'wpsASN' => $WPS_Segment['ASN'],
        'wpsUCN' => $WPS_Segment['UCN'],
        'wpsSPL' => $WPS_Segment['SPL'],
        'wpsUST' => $WPS_Segment['UST'],
    ];
    
    if ($faker->boolean) {
        $NHS_Segment = factory(NHS_Segment::class)->raw();
        
        $attributes['nhsMFR'] = $NHS_Segment['MFR'];
        $attributes['nhsMPN'] = $NHS_Segment['MPN'];
        $attributes['nhsSER'] = $NHS_Segment['SER'];
        $attributes['nhsMFN'] = $NHS_Segment['MFN'];
        $attributes['nhsPNR'] = $NHS_Segment['PNR'];
        $attributes['nhsOPN'] = $NHS_Segment['OPN'];
        $attributes['nhsUSN'] = $NHS_Segment['USN'];
        $attributes['nhsPDT'] = $NHS_Segment['PDT'];
        $attributes['nhsASN'] = $NHS_Segment['ASN'];
        $attributes['nhsUCN'] = $NHS_Segment['UCN'];
        $attributes['nhsSPL'] = $NHS_Segment['SPL'];
        $attributes['nhsUST'] = $NHS_Segment['UST'];
        $attributes['nhsNPN'] = $NHS_Segment['NPN'];
    
    }
    
    if ($faker->boolean) {
        $RPS_Segment = factory(RPS_Segment::class)->raw();
        
        $attributes['rpsMPN'] = $RPS_Segment['MPN'];
        $attributes['rpsMFR'] = $RPS_Segment['MFR'];
        $attributes['rpsMFN'] = $RPS_Segment['MFN'];
        $attributes['rpsSER'] = $RPS_Segment['SER'];
        $attributes['rpsPNR'] = $RPS_Segment['PNR'];
        $attributes['rpsOPN'] = $RPS_Segment['OPN'];
        $attributes['rpsUSN'] = $RPS_Segment['USN'];
        $attributes['rpsASN'] = $RPS_Segment['ASN'];
        $attributes['rpsUCN'] = $RPS_Segment['UCN'];
        $attributes['rpsSPL'] = $RPS_Segment['SPL'];
        $attributes['rpsUST'] = $RPS_Segment['UST'];
        $attributes['rpsPDT'] = $RPS_Segment['PDT'];
    }
    
    $attributes['reversal_id'] = NULL;
    
    return $attributes;
});

$factory->state(NotificationPiecePart::class, 'all_segments', function (Faker $faker) {
    
    $NHS_Segment = factory(NHS_Segment::class)->raw();
    $RPS_Segment = factory(RPS_Segment::class)->raw();
    $WPS_Segment = factory(WPS_Segment::class)->raw();
    
    $notification = NULL;
    
    return [
        'id' => $WPS_Segment['PPI'],
        'notification_id' => function(){
            return factory(Notification::class)->create()->id;
        },
        'wpsSFI' => function (){
            return DB::getPdo()->lastInsertId();
        },
        'wpsPPI' => $WPS_Segment['PPI'],
        'wpsPFC' => $WPS_Segment['PFC'],
        'wpsMFR' => $WPS_Segment['MFR'],
        'wpsMFN' => $WPS_Segment['MFN'],
        'wpsMPN' => $WPS_Segment['MPN'],
        'wpsSER' => $WPS_Segment['SER'],
        'wpsFDE' => $WPS_Segment['FDE'],
        'wpsPNR' => $WPS_Segment['PNR'],
        'wpsOPN' => $WPS_Segment['OPN'],
        'wpsUSN' => $WPS_Segment['USN'],
        'wpsPDT' => $WPS_Segment['PDT'],
        'wpsGEL' => $WPS_Segment['GEL'],
        'wpsMRD' => $WPS_Segment['MRD'],
        'wpsASN' => $WPS_Segment['ASN'],
        'wpsUCN' => $WPS_Segment['UCN'],
        'wpsSPL' => $WPS_Segment['SPL'],
        'wpsUST' => $WPS_Segment['UST'],
        
        'nhsMFR' => $NHS_Segment['MFR'],
        'nhsMPN' => $NHS_Segment['MPN'],
        'nhsSER' => $NHS_Segment['SER'],
        'nhsMFN' => $NHS_Segment['MFN'],
        'nhsPNR' => $NHS_Segment['PNR'],
        'nhsOPN' => $NHS_Segment['OPN'],
        'nhsUSN' => $NHS_Segment['USN'],
        'nhsPDT' => $NHS_Segment['PDT'],
        'nhsASN' => $NHS_Segment['ASN'],
        'nhsUCN' => $NHS_Segment['UCN'],
        'nhsSPL' => $NHS_Segment['SPL'],
        'nhsUST' => $NHS_Segment['UST'],
        'nhsNPN' => $NHS_Segment['NPN'],
        
        'rpsMPN' => $RPS_Segment['MPN'],
        'rpsMFR' => $RPS_Segment['MFR'],
        'rpsMFN' => $RPS_Segment['MFN'],
        'rpsSER' => $RPS_Segment['SER'],
        'rpsPNR' => $RPS_Segment['PNR'],
        'rpsOPN' => $RPS_Segment['OPN'],
        'rpsUSN' => $RPS_Segment['USN'],
        'rpsASN' => $RPS_Segment['ASN'],
        'rpsUCN' => $RPS_Segment['UCN'],
        'rpsSPL' => $RPS_Segment['SPL'],
        'rpsUST' => $RPS_Segment['UST'],
        'rpsPDT' => $RPS_Segment['PDT'],
        'reversal_id' => NULL
    ];
});