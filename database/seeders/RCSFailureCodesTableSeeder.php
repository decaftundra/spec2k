<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RCSFailureCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('rcs_failure_codes')->truncate();
        
        DB::table('rcs_failure_codes')->insert([
            
            // Reordered.
            ['RRC' => 'U', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'U', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            
            ['RRC' => 'S', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'S', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            
            ['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'M', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'P', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
            ['RRC' => 'O', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}

/*
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'CM', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NM', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'CB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NB'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'CR', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NC', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'HW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'NI', 'FHS' => 'SW', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'M', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'S', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'FT', 'FFI' => 'IN', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'M', 'FFC' => 'NA', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'O', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'P', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'S', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
['RRC' => 'U', 'FFC' => 'NT', 'FFI' => 'NA', 'FHS' => 'NA', 'FCR' => 'NA', 'FAC' => 'NA', 'FBC' => 'NA'],
*/