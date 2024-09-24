<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AddAcronymToUsers extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_acronyms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a unique acronym to each user.';

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
        $users = User::whereNull('acronym')->get();
        
        if (count($users)) {
            foreach ($users as $user) {
                $user->acronym = User::createAcronym($user->first_name, $user->last_name);
                $user->save();
            }
        }
    }
}
