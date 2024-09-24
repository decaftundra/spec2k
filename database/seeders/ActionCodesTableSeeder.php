<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActionCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('action_codes')->truncate();
        
        DB::table('action_codes')->insert([
            ['SAC' => 'IRTR', 'RFI' => 0],
            ['SAC' => 'SCRP', 'RFI' => 0],
            ['SAC' => 'XCHG', 'RFI' => 0],
            ['SAC' => 'RPLC', 'RFI' => 0],
            ['SAC' => 'RTAS', 'RFI' => 0],
            ['SAC' => 'RCRT', 'RFI' => 1],
            ['SAC' => 'REPR', 'RFI' => 1],
            ['SAC' => 'BERP', 'RFI' => 0],
            ['SAC' => 'CLBN', 'RFI' => 1],
            ['SAC' => 'MODN', 'RFI' => 1],
            ['SAC' => 'OVHL', 'RFI' => 1],
            ['SAC' => 'REFN', 'RFI' => 1],
            ['SAC' => 'RLSW', 'RFI' => 1],
            ['SAC' => 'ROMP', 'RFI' => 1],
            ['SAC' => 'RPCK', 'RFI' => NULL],
            ['SAC' => 'RWRK', 'RFI' => NULL],
            ['SAC' => 'SADJ', 'RFI' => 1],
            ['SAC' => 'SLRN', 'RFI' => 1],
            ['SAC' => 'SPAG', 'RFI' => NULL],
            ['SAC' => 'TEST', 'RFI' => NULL],
            ['SAC' => 'UNRP', 'RFI' => 0],
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}