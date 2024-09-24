<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CageCodeLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('cage_code_location')->truncate();
        
        DB::table('cage_code_location')->insert([
            ['cage_code_id' => 14, 'location_id' => 3],
            ['cage_code_id' => 1, 'location_id' => 12],
            ['cage_code_id' => 2, 'location_id' => 13],
            ['cage_code_id' => 3, 'location_id' => 13],
            ['cage_code_id' => 5, 'location_id' => 10],
            ['cage_code_id' => 6, 'location_id' => 11],
            ['cage_code_id' => 7, 'location_id' => 15],
            ['cage_code_id' => 8, 'location_id' => 1],
            ['cage_code_id' => 9, 'location_id' => 9],
            ['cage_code_id' => 10, 'location_id' => 7],
            ['cage_code_id' => 11, 'location_id' => 17],
            ['cage_code_id' => 12, 'location_id' => 18],
            ['cage_code_id' => 13, 'location_id' => 19],
            ['cage_code_id' => 15, 'location_id' => 8],
            ['cage_code_id' => 4, 'location_id' => 14],
            ['cage_code_id' => 18, 'location_id' => 4],
            ['cage_code_id' => 4, 'location_id' => 21],
            ['cage_code_id' => 17, 'location_id' => 5],
            ['cage_code_id' => 20, 'location_id' => 8],
            ['cage_code_id' => 21, 'location_id' => 8],
            ['cage_code_id' => 16, 'location_id' => 20],
            ['cage_code_id' => 22, 'location_id' => 22],
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}