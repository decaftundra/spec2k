<?php

namespace App\Console\Commands;

use App\User;
use App\Location;
use App\Mail\UserRegistered;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AddCoventrySAndSUsers extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_coventry_s_and_s_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add users for Coventry S&S plant.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = [
            ['first_name' => 'Kirstie', 'last_name' => 'Wilkinson', 'email' => 'kirstie.wilkinson@meggitt.com', 'role_id' => 2],
            ['first_name' => 'Arran', 'last_name' => 'Davis', 'email' => 'arran.davies@meggitt.com', 'role_id' => 2],
            ['first_name' => 'John', 'last_name' => 'Dickson', 'email' => 'John.Dickson@meggitt.com', 'role_id' => 2],
            ['first_name' => 'Hayley', 'last_name' => 'Johnson', 'email' => 'Hayley.Johnson@meggitt.com', 'role_id' => 2],
            ['first_name' => 'Agnieszka', 'last_name' => 'Tymecka', 'email' => 'agnieska.tymecka@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Richard', 'last_name' => 'Kasper', 'email' => 'richard.kasper@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Diane', 'last_name' => 'Smith', 'email' => 'diane.smith@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Lauren', 'last_name' => 'Bradley', 'email' => 'lauren.bradley@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Harina', 'last_name' => 'Sandhu', 'email' => 'Harina.Sandhu@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Laura', 'last_name' => 'Kelly', 'email' => 'Laura.Kelly@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Laura', 'last_name' => 'Sheehan', 'email' => 'Laura.Sheehan@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Amanda', 'last_name' => 'Routley', 'email' => 'Amanda.Routley@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Kathy', 'last_name' => 'Kelly', 'email' => 'Kathy.Kelly@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Claire', 'last_name' => 'Morgan', 'email' => 'Claire.Morgan@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Michael', 'last_name' => 'Stanley Jnr', 'email' => 'Mick.Stanley@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Lisa', 'last_name' => 'Nutt', 'email' => 'Lisa.Nutt@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Andy', 'last_name' => 'Margrett', 'email' => 'Andy.Margrett@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Ryan', 'last_name' => 'Wake', 'email' => 'Ryan.Wake@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Nicola', 'last_name' => 'Edge', 'email' => 'Nicola.Edge@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Caroline', 'last_name' => 'Davis', 'email' => 'Caroline.Davis@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Chris', 'last_name' => 'Marsden', 'email' => 'Chris.Marsden@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Joe', 'last_name' => 'Naylor', 'email' => 'Joe.Naylor@meggitt.com', 'role_id' => 1],
            ['first_name' => 'David', 'last_name' => 'Thompson', 'email' => 'David.Thompson@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Frederico', 'last_name' => 'Benge', 'email' => 'Frederico.Benge@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Rupert', 'last_name' => 'Anderson', 'email' => 'Rupert.Anderson@meggitt.com', 'role_id' => 1],
            ['first_name' => 'James', 'last_name' => 'Docherty', 'email' => 'James.Doherty@meggitt.com', 'role_id' => 1],
            ['first_name' => 'Lisa', 'last_name' => 'Edmunds', 'email' => 'lisa.edmunds@meggitt.com', 'role_id' => 1]
        ];
        
        $location = Location::where('name', 'S&S Coventry')->firstOrFail();
        
        foreach ($users as $user) {
            if (!User::where('email', $user['email'])->first()) {
                $newUser = new User;
                $newUser->first_name = $user['first_name'];
                $newUser->last_name = $user['last_name'];
                $newUser->email = $user['email'];
                $newUser->role_id = $user['role_id'];
                $newUser->location_id = $location->id;
                $newUser->password = Hash::make(Str::random(32));
                $newUser->planner_group = NULL;
                $newUser->acronym = User::createAcronym($newUser->first_name, $newUser->last_name);
        
                $newUser->save();
        
                Mail::to($newUser)->send(new UserRegistered($newUser));
                
                $this->line($user['first_name'] . ' ' . $user['last_name'] . ' added successfully!');
            } else {
                $this->error($user['first_name'] . ' ' . $user['last_name'] . ' could not be added as they already exist.');
            }
        }
    }
}
