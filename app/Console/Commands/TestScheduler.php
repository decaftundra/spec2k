<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class TestScheduler extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:test_scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email to mark@interactivedimension.com';

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
        Mail::raw('Hello from Spec 2k Scheduler', function ($message) {
            $message->subject('Spec2k Scheduler')
                ->from('spec2kapp@interactivedimension.com')
                ->to('mark@interactivedimension.com');
        });
    }
}
