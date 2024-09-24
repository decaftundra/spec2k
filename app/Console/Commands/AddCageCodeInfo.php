<?php

namespace App\Console\Commands;

use App\CageCode;
use Illuminate\Console\Command;

class AddCageCodeInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_cage_code_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds cage code info.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cageCodeInfos = [
            '25693' => 'Meggitt Safety Systems Inc., Simi Valley, CA, USA',
            '45402' => 'Meggitt Safety Systems Inc., Ventura County, CA, USA',
            '56221' => 'Meggitt Inc., Rockmart, GA, USA',
            '78385' => 'Meggitt Inc., Troy, IN, USA',
            '79318' => 'Meggitt Inc., North Hollywood, CA, USA',
            '95266' => 'OECO, Milwaukie, OR, USA',
            '95411' => 'Meggitt Orange County, CA, USA',
            '05167' => 'Meggitt Safery Systems, Simi Valley, CA, USA',
            '0B9R9' => 'Meggitt Aircraft Braking Systems Corp., Akron, OH, USA',
            '1B1H6' => 'Pacific Scientific Company Div Services & Support, Miami, FL, USA',
            '6S4S0' => 'Meggitt Aircraft Braking Systems Corp., Danville, KY, USA',
            'F1549' => 'Artus, Avrillé, France',
            'F5496' => 'AEVA, Fleac, France',
            'F9238' => 'Meggitt Sensorex, Archamps, France',
            'K0802' => 'Meggitt UK, Basingstoke, UK',
            'K1037' => 'Meggitt Aircraft Braking Systems, Coventry, UK',
            'S3960' => 'Meggitt SA, Fribourg, Switzerland',
            'U1596' => 'Meggitt UK, Dunstable, UK',
            'U1901' => 'Meggitt Aerospace Ltd, Loughborough, UK',
            'U6578' => 'Meggitt Control Systems, Birmingham, UK',
            'U8976' => 'Meggitt Aerospace Ltd, Coventry, UK',
        ];
        
        foreach ($cageCodeInfos as $code => $info) {
            $cageCode = CageCode::where('cage_code', $code)->first();
            
            if ($cageCode) {
                $cageCode->info = $info;
                $cageCode->save();
            }
        }
        
        return Command::SUCCESS;
    }
}