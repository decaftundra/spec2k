<?php

namespace App\Console\Commands;

use App\NotificationPiecePart;
use Illuminate\Console\Command;

class CleanUpUndeletedPiecePartReversals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:clean_up_reversals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stray piece part reversals that were not originally deleted in testing.';

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
        NotificationPiecePart::whereNotNull('reversal_id')->delete();
    }
}
