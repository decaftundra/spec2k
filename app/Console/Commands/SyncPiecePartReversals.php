<?php

namespace App\Console\Commands;

use App\Events\NotificationPiecePartReversal;
use App\NotificationPiecePart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncPiecePartReversals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:sync_reversals {piecePartIds?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Piece Part reversals.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $piecePartIds = $this->argument('piecePartIds');
        
        $piecePartIds = array_filter($piecePartIds); // Remove empty ids.
        
        if ($piecePartIds && count($piecePartIds)) {
            
            Log::info('Number of Piece Part Ids in sync reversal command: ' . count($piecePartIds));
            Log::info('Piece Part Ids in sync reversal command:', [$piecePartIds]);
            
            $piecePartReversals = NotificationPiecePart::withTrashed()->whereIn('id', $piecePartIds)->get();
        } else {
            Log::info('DOING ELSE CLAUSE IN SYNC REVERSALS COMMAND.');
            $piecePartReversals = NotificationPiecePart::whereNotNull('reversal_id')->get();
        }
        
        if ($piecePartReversals && count($piecePartReversals)) {
            
            Log::info('Number of Piece Part Ids sent to sync reversal event: ' . count($piecePartReversals));
            foreach ($piecePartReversals as $piecePart) {
                event(new NotificationPiecePartReversal($piecePart));
            }
        }
    }
}
