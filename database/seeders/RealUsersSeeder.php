<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        DB::table('users')->insert([
            [
                'first_name' => 'Mark',
                'last_name' => 'Tierney',
                'email' => 'mark@interactivedimension.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 1101)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Ian',
                'last_name' => 'Minto',
                'email' => 'Ian.Minto@meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 1101)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Nicola',
                'last_name' => 'Aloe',
                'email' => 'Nicola.Aloe@ch.meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Joël',
                'last_name' => 'Berset',
                'email' => 'Joel.Berset@ch.meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Frédéric',
                'last_name' => 'Blanc',
                'email' => 'Frederic.Blanc@ch.meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Boca',
                'email' => 'Kevin.Boca@ch.meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Martin',
                'email' => 'Daniel.Martin@ch.meggitt.com',
                'role_id' => 2,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Sylvia',
                'last_name' => 'Bonny',
                'email' => 'Sylvia.Bonny@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Justin',
                'last_name' => 'Chassot',
                'email' => 'Justin.Chassot@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Victor',
                'last_name' => 'De Campos',
                'email' => 'Victor.De.Campos@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Alexandra',
                'last_name' => 'Genoud',
                'email' => 'alexandra.genoud@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Pierre-Andre',
                'last_name' => 'Gilliard',
                'email' => 'Pierre-Andre.Gilliard@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Jonas',
                'last_name' => 'Gutknecht',
                'email' => 'Jonas.Gutknecht@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Joachim',
                'last_name' => 'Klaus',
                'email' => 'Joachim.Klaus@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Litandi',
                'email' => 'Christian.Litandi@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Anne',
                'last_name' => 'Martignoni',
                'email' => 'Anne.Martignoni@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Fabio',
                'last_name' => 'Medinas',
                'email' => 'Fabio.Medinas@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Jerôme',
                'last_name' => 'Mettraux',
                'email' => 'Jerome.Mettraux@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Karen',
                'last_name' => 'Muamba',
                'email' => 'Karen.Muamba@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'The Tai',
                'last_name' => 'Nguyen',
                'email' => 'Thetai.Nguyen@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Stefano',
                'last_name' => 'Nori',
                'email' => 'Stefano.Nori@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Jean-Marc',
                'last_name' => 'Simon',
                'email' => 'Jean-Marc.Simon@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ],
            [
                'first_name' => 'Carmen',
                'last_name' => 'Théraulaz',
                'email' => 'Carmen.Theraulaz@ch.meggitt.com',
                'role_id' => 1,
                'location_id' => App\Location::where('plant_code', 2200)->first()->id,
                'password' => Hash::make(Str::random(26)),
                'remember_token' => Str::random(10)
            ]
        ]);
        
        App\User::boot(); // Reboot model to register model events events.
    }
}








