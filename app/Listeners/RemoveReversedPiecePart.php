<?php

namespace App\Listeners;

use App\NotificationPiecePart;
use App\PieceParts\PiecePart;
use App\PieceParts\PiecePartDetail;
use App\Events\NotificationPiecePartReversal;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class RemoveReversedPiecePart
{
    /**
     * Handle the event.
     *
     * @param  NotificationPiecePartCreated  $event
     * @return void
     */
    public function handle(NotificationPiecePartReversal $event)
    {
        //Log::info('NotificationPiecePartReversal event fired.');
        
        if ($event->notificationPiecePart->isReversal()) {
            
            //Log::info('Notification Piece Part id:' . $event->notificationPiecePart->id . ' is a reversal.');
            
            // Get the piece part record from the same notification that needs to be removed.
            $reversedPiecePart = NotificationPiecePart::withTrashed()->where('notification_id', $event->notificationPiecePart->notification_id)
                ->where('id', $event->notificationPiecePart->reversal_id)
                ->first();
                
            if ($reversedPiecePart) {
                Log::info('Deleting notification piece part id:' . $reversedPiecePart->id);
                
                // Delete the reversed notification piece part
                $reversedPiecePart->delete();
            } else {
                Log::info('Could not find reversed piece part with id: ' . $event->notificationPiecePart->reversal_id . '. Possibly already deleted.');
            }
            
            // Delete the referenced reversal.
            $piecePartDetail = PiecePartDetail::withTrashed()->with('WPS_Segment', 'NHS_Segment', 'RPS_Segment')
                ->find($event->notificationPiecePart->reversal_id);
                
            // Delete the saved piece part if exists.
            if ($piecePartDetail) {
                    
                Log::info('Deleting piece part detail id:' . $piecePartDetail->id);
                
                // Delete the related segments first.
                if ($piecePartDetail->WPS_Segment) $piecePartDetail->WPS_Segment->delete();
                if ($piecePartDetail->NHS_Segment) $piecePartDetail->NHS_Segment->delete();
                if ($piecePartDetail->RPS_Segment) $piecePartDetail->RPS_Segment->delete();
                    
                // Then delete the piece part detail.
                $piecePartDetail->delete();
            } else {
                Log::info('No saved piece part detail with id: ' . $event->notificationPiecePart->reversal_id . ' to delete for reversal. Possibly already deleted or not yet in progress.');
            }
            
            // Then delete the saved reversal.
            $piecePartDetail = PiecePartDetail::withTrashed()->with('WPS_Segment', 'NHS_Segment', 'RPS_Segment')
                ->find($event->notificationPiecePart->id);
                
            // Delete the saved piece part if exists.
            if ($piecePartDetail) {
                    
                Log::info('Deleting piece part detail id:' . $piecePartDetail->id);
                
                // Delete the related segments first.
                if ($piecePartDetail->WPS_Segment) $piecePartDetail->WPS_Segment->delete();
                if ($piecePartDetail->NHS_Segment) $piecePartDetail->NHS_Segment->delete();
                if ($piecePartDetail->RPS_Segment) $piecePartDetail->RPS_Segment->delete();
                    
                // Then delete the piece part detail.
                $piecePartDetail->delete();
            } else {
                Log::info('No saved piece part detail with id: ' . $event->notificationPiecePart->reversal_id . ' to delete for reversal. Possibly already deleted or not yet in progress.');
            }
            
            // Finally delete the reversal.
            $event->notificationPiecePart->delete();
        } else {
            Log::error('Notification Piece Part id:' . $event->notificationPiecePart->id . ' is NOT a reversal.');
        }
    }
}
