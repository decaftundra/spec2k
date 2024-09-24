<?php

use App\Role;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'user')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
        'planner_group' => function() use ($faker) {
            do {
                $plannerGroup = $faker->unique()->bothify('?##');
            } while (DB::table('users')->where('planner_group', $plannerGroup)->count());
            
            return $faker->boolean ? strtoupper($plannerGroup) : NULL;
        },
        'acronym' => App\User::createAcronym($firstName, $lastName)
    ];
});

$factory->state(App\User::class, 'user', function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'user')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
        'planner_group' => function() use ($faker) {
            do {
                $plannerGroup = $faker->unique()->bothify('?##');
            } while (DB::table('users')->where('planner_group', $plannerGroup)->count());
            
            return $faker->boolean ? strtoupper($plannerGroup) : NULL;
        },
        'acronym' => App\User::createAcronym($firstName, $lastName)
    ];
});

$factory->state(App\User::class, 'admin', function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'admin')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
        'planner_group' => function() use ($faker) {
            do {
                $plannerGroup = $faker->unique()->bothify('?##');
            } while (DB::table('users')->where('planner_group', $plannerGroup)->count());
            
            return $faker->boolean ? strtoupper($plannerGroup) : NULL;
        },
        'acronym' => App\User::createAcronym($firstName, $lastName)
    ];
});

$factory->state(App\User::class, 'site_admin', function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'site_admin')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
    ];
});

$factory->state(App\User::class, 'data_admin', function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'data_admin')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
    ];
});

$factory->state(App\User::class, 'inactive', function (Faker $faker) {
    
    $allowedDomains = App\User::getAllowedDomains();
    $randomKey = array_rand($allowedDomains);
    
    $uniqueSuffix = Str::random(10);
    $domain = $allowedDomains[$randomKey];
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
    
    $role = Role::where('name', 'inactive')->firstOrFail();
    
    return [
        'role_id' => $role->id,
        'location_id' => function() {
            return App\Location::inRandomOrder()->first()->id;
        },
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
    ];
});