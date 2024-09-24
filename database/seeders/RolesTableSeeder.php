<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('roles')->truncate();
        
        DB::table('roles')->insert([
            ['name' => 'user', 'rank' => 10],
            ['name' => 'admin', 'rank' => 20],
            ['name' => 'site_admin', 'rank' => 30],
            ['name' => 'data_admin', 'rank' => 100],
            ['name' => 'inactive', 'rank' => 0]
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}
