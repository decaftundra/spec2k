<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AppVersionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
		
		DB::table('app_versions')->truncate();
		
		DB::table('app_versions')->insert([
            ['app_version' => '1.0.0', 'git_commit_reference_dev' => Str::random(7), 'git_commit_reference_live' => Str::random(7)],
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}
