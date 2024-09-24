<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment(['live', 'production'])) {
            $this->call([
                RolesTableSeeder::class,
                LocationsTableSeeder::class,
                CageCodesTableSeeder::class,
                CageCodeLocationTableSeeder::class,
                ActionCodesTableSeeder::class,
                UpdatedUtasCodesTableSeeder::class,
                UpdatedUtasReasonCodesTableSeeder::class,
                UpdatedUtasPartNumbersTableSeeder::class,
                
                RCSFailureCodesTableSeeder::class,
                AircraftDetailsTableSeeder::class,
                CustomersTableSeeder::class,
                RealUsersSeeder::class,
                MessagesTableSeeder::class,
            ]);
        } else {
            $this->call([
                RolesTableSeeder::class,
                LocationsTableSeeder::class,
                CageCodesTableSeeder::class,
                CageCodeLocationTableSeeder::class,
                ActionCodesTableSeeder::class,
                UpdatedUtasCodesTableSeeder::class,
                UpdatedUtasReasonCodesTableSeeder::class,
                UpdatedUtasPartNumbersTableSeeder::class,
                
                RCSFailureCodesTableSeeder::class,
                UsersTableSeeder::class,
                CustomersTableSeeder::class,
                AircraftDetailsTableSeeder::class,
                RealNotificationsTableSeeder::class,
                MessagesTableSeeder::class,
                AppVersionsTableSeeder::class,
            ]);
        }
    }
}