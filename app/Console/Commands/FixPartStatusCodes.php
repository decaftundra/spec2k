<?php

namespace App\Console\Commands;

use App\ShopFindings\SAS_Segment;
use Illuminate\Console\Command;

class FixPartStatusCodes extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:fix_part_status_codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts all PSC values in the SAS_Segment to uppercase.';

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
        $SAS_Segments = SAS_Segment::all();
        
        if (count($SAS_Segments)) {
            foreach ($SAS_Segments as $SAS_Segment) {
                $SAS_Segment->PSC = strtoupper($SAS_Segment->PSC);
                $SAS_Segment->save();
            }
        }
    }
}
