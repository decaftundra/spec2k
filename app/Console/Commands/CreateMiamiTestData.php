<?php

namespace App\Console\Commands;

use App\Location;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateMiamiTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:create_miami_test_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates two basic notifications for testing Miami Collins data.';

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
        /*
        
        Notification number 000350600000
        Receipt Date – 14/05/2022
        Ser No: Collins Test1
        Part No: 1020316-1
         
        Notification number 000350600001
        Receipt Date – 14/05/2022
        Ser No: Collins Test2
        Part No: 1023141-4
        
        */
        
        $notification1 = new Notification;
        $notification1->id = '000350600000';
        $notification1->plant_code = 3515;
        $notification1->hdrROC = Location::getFirstCageCode(3515);
        $notification1->hdrRON = Location::getReportingOrganisationName(3515);
        $notification1->rcsSFI = '000350600000';
        $notification1->rcsMRD = Carbon::createFromFormat('d/m/Y', '14/05/2022');
        $notification1->rcsMPN = '1020316-1';
        $notification1->rcsSER = 'Collins Test1';
        $notification1->status = 'in_progress';
        $notification1->save();
        
        $notification2 = new Notification;
        $notification2->id = '000350600001';
        $notification2->plant_code = 3515;
        $notification2->hdrROC = Location::getFirstCageCode(3515);
        $notification2->hdrRON = Location::getReportingOrganisationName(3515);
        $notification2->rcsSFI = '000350600001';
        $notification2->rcsMRD = Carbon::createFromFormat('d/m/Y', '14/05/2022');
        $notification2->rcsMPN = '1023141-4';
        $notification2->rcsSER = 'Collins Test2';
        $notification2->status = 'in_progress';
        $notification2->save();
        
        return Command::SUCCESS;
    }
}
