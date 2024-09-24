<?php

namespace App\Listeners;

use Log;
use App\Events\PiecePartsBatchSave;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateValidationOnPiecePartsBatchSave
{
    /**
     * Handle the event.
     *
     * @param  PiecePartsBatchSave  $event
     * @return void
     */
    public function handle(PiecePartsBatchSave $event)
    {
        $shopFinding = $event->shopFinding->load([
            'PiecePart.PiecePartDetails.WPS_Segment',
            'PiecePart.PiecePartDetails.NHS_Segment',
            'PiecePart.PiecePartDetails.RPS_Segment'
        ]);
        
        if ($shopFinding->PiecePart && count($shopFinding->PiecePart->PiecePartDetails)) {
            foreach ($shopFinding->PiecePart->PiecePartDetails as $PiecePartDetail) {
                if ($PiecePartDetail->WPS_Segment) {
                    $PiecePartDetail->WPS_Segment->setIsValid('PiecePartsBatchSave');
                }
                
                if ($PiecePartDetail->NHS_Segment) {
                    $PiecePartDetail->NHS_Segment->setIsValid('PiecePartsBatchSave');
                }
                
                if ($PiecePartDetail->RPS_Segment) {
                    $PiecePartDetail->RPS_Segment->setIsValid('PiecePartsBatchSave');
                }
            }
        }
        
        $shopFinding->setIsValid('PiecePartsBatchSave');
    }
}
