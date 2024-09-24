<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixNHSSegmentFields extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:fix_nhs_fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs NHS fields that may have been missed on import.';

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
        // This will check for any old piece parts that weren't in the piece parts text file on import that need the NHS fields updating.
        $sql = "update notification_piece_parts
                inner join notifications on notification_piece_parts.notification_id = notifications.id
                set notification_piece_parts.nhsMPN = notifications.rcsMPN,
            	notification_piece_parts.nhsPNR = LEFT(notifications.rcsMPN, 15),
            	notification_piece_parts.nhsMFR = notifications.rcsMFR,
            	notification_piece_parts.nhsSER = notifications.rcsSER";
            
        Schema::disableForeignKeyConstraints();
        DB::statement($sql);
        Schema::enableForeignKeyConstraints();
    }
}
