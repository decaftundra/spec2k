<?php

namespace Database\Seeders;

use App\User;
use App\Location;
use App\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::flushEventListeners(); // Prevents activities being recorded during seeding.

    $locations = Location::get();

    foreach ($locations as $location) {
      factory(User::class, 3)->states('user')->create(['location_id' => $location->id]);
      factory(User::class, 3)->states('admin')->create(['location_id' => $location->id]);
      factory(User::class, 2)->states('site_admin')->create(['location_id' => $location->id]);
      factory(User::class, 2)->states('data_admin')->create(['location_id' => $location->id]);
      factory(User::class, 2)->states('inactive')->create(['location_id' => $location->id]);
    }

    //factory(App\User::class, 'data_admin', 1)->create();
    /*factory(App\User::class, 'admin')->create([
            'first_name'    => 'Mark',
            'last_name'     => 'Tierney',
            'email'         => 'mark@interactivedimension.com'
        ]);*/

    //User::boot(); // Reboot model to register model events events.

    DB::table('users')->insert([
      [
        'role_id'       => 2,
        'location_id'   => 11,
        'first_name'    => 'Lindsay',
        'last_name'     => 'Manning',
        'email'         => 'developers@thefusionworks.com',
        'password' => Hash::make('changeme'),
        'remember_token' => Str::random(10),
      ],
    ]);
    User::boot();
  }
}
