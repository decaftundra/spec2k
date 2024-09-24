<?php

namespace App\Console\Commands;

use App\ShopFindings\Misc_Segment;
use Illuminate\Console\Command;

class RemoveSourceDataFromMiscSegmentValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:remove_source_data_from_misc_segment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes source_data value from Misc Segment values, this is cached data that should not be stored here.';

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
     * @return int
     */
    public function handle()
    {
        $misc_segments = Misc_Segment::all();
        
        if (count($misc_segments)) {
            foreach ($misc_segments as $segment) {
                $values = $segment->values;
                
                $values = json_decode($values);
                
                if (isset($values->source_data)) {
                    unset($values->source_data);
                    $segment->values = json_encode($values);
                    $segment->save();
                }
            }
        }
        
        return Command::SUCCESS;
    }
}
