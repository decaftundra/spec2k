<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UtasPartNumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
		
		DB::table('utas_part_numbers')->truncate();
		
		DB::table('utas_part_numbers')->insert([
            ['meggitt_part_no' => '521575-1', 'description' => 'Buffer shutoff valve', 'utas_part_no' => '1023422'],
            ['meggitt_part_no' => '520985-2', 'description' => 'High pressure compressor bleed valve', 'utas_part_no' => '1020316-1'],
            ['meggitt_part_no' => '521765', 'description' => 'Low pressure compressor buffer air valve', 'utas_part_no' => '1020923-1'],
            ['meggitt_part_no' => '521225-1', 'description' => 'High pressure compressor bleed valve', 'utas_part_no' => '1020924-1'],
            ['meggitt_part_no' => '521435', 'description' => 'Starter Air Valve', 'utas_part_no' => '1021498-2'],
            ['meggitt_part_no' => '521435-1', 'description' => 'Starter Air Valve', 'utas_part_no' => '1021498-3'],
            ['meggitt_part_no' => '521415-1', 'description' => 'Active Clearance Control Valve', 'utas_part_no' => '1023141-2'],
            ['meggitt_part_no' => '521415-2', 'description' => 'Active Clearance Control Valve', 'utas_part_no' => '1023141-3'],
            ['meggitt_part_no' => '521415-3', 'description' => 'Active Clearance Control Valve', 'utas_part_no' => '1023141-4'],
            ['meggitt_part_no' => '521235-2', 'description' => 'High pressure compressor bleed valve', 'utas_part_no' => '1023640-2'],
            ['meggitt_part_no' => '520975-2', 'description' => 'Engine Starter Air Valve', 'utas_part_no' => '1023662'],
            ['meggitt_part_no' => '522755', 'description' => 'Engine Starter Air Valve', 'utas_part_no' => '1023662-2'],
            ['meggitt_part_no' => '522755-1', 'description' => 'Engine Starter Air Valve', 'utas_part_no' => '1023662-3'],
            ['meggitt_part_no' => '521815', 'description' => 'Engine Starter Air Valve', 'utas_part_no' => '1024212-2'],
            ['meggitt_part_no' => '522025', 'description' => 'HPC Bleed Check Valve-Active', 'utas_part_no' => '1025044-2']
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}