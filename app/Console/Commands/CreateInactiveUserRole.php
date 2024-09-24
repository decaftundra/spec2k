<?php

namespace App\Console\Commands;

use App\Role;
use Illuminate\Console\Command;

class CreateInactiveUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:create_inactive_user_role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an inactive user role.';

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
        if (!Role::where('name', 'inactive')->first()) {
            $role = new Role;
            $role->name = 'inactive';
            $role->save();
        }
        
        return 0;
    }
}
