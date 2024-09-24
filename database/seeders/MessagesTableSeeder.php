<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('messages')->insert([
            ['name' => 'SAP Feed Error', 'level' => 'error'],
            ['name' => 'General Error', 'level' => 'error'],
        ]);
    }
}
