<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AddPlannerGroupToUser extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_planner_group_to_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the planner group to the relevant users.';

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
        $plannerGroups = [
            'Z01' => 'Jerome.Mettraux@ch.meggitt.com',
            'Z03' => 'Joel.Berset@ch.meggitt.com',
            'Z04' => 'Nicola.Aloe@ch.meggitt.com',
            'Z05' => 'Jean-Marc.Simon@ch.meggitt.com',
            'Z06' => 'Jonas.Gutknecht@ch.meggitt.com',
            'Z07' => 'Stefano.Nori@ch.meggitt.com',
            'Z10' => 'Pierre-Andre.Gilliard@ch.meggitt.com',
            'Z13' => 'Victor.De.Campos@ch.meggitt.com',
            'Z15' => 'Karen.Muamba@ch.meggitt.com',
            'Z16' => 'Fabio.Medinas@ch.meggitt.com',
            'Z17' => 'Christian.Litandi@ch.meggitt.com',
            'Z22' => 'Joachim.Klaus@ch.meggitt.com',
            'Z24' => 'Justin.Chassot@ch.meggitt.com'
        ];
        
        foreach ($plannerGroups as $group => $email) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $user->planner_group = $group;
                $user->save();
            }
        }
    }
}
