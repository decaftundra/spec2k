<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CageCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('cage_codes')->truncate();
        
        DB::table('cage_codes')->insert([
            ['cage_code' => 'F5496'],
            ['cage_code' => '0B9R9'],
            ['cage_code' => '6S4S0'],
            ['cage_code' => 'K1037'],
            ['cage_code' => 'U6578'],
            ['cage_code' => 'U8976'],
            ['cage_code' => 'U1596'],
            ['cage_code' => '79318'],
            ['cage_code' => '78385'],
            ['cage_code' => '95411'],
            ['cage_code' => '56221'],
            ['cage_code' => 'F9238'],
            ['cage_code' => 'U1901'],
            ['cage_code' => 'S3960'],
            ['cage_code' => '25693'],
            ['cage_code' => 'K0802'],
            ['cage_code' => 'F8769'],
            ['cage_code' => 'F1549'],
            ['cage_code' => '95266'],
            ['cage_code' => '05167'],
            ['cage_code' => '45402'],
            ['cage_code' => '1B1H6'],
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}
