<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('locations')->truncate();
        
        DB::table('locations')->insert([
            ['timezone' => 'America/Los_Angeles', 'id' => 1, 'sap_location_name' => 'MCS North Hollywood', 'name' => 'North Hollywood', 'plant_code' => 3101],
            ['timezone' => 'Europe/Zurich', 'id' => 3, 'sap_location_name' => 'Meggitt SA', 'name' => 'Meggitt SA', 'plant_code' => 2200],
            ['timezone' => 'Europe/Paris', 'id' => 4, 'sap_location_name' => 'Artus', 'name' => 'Artus', 'plant_code' => 2500],
            ['timezone' => 'Europe/Paris', 'id' => 5, 'sap_location_name' => 'TFE', 'name' => 'TFE', 'plant_code' => 2505],
            ['timezone' => 'America/Los_Angeles', 'id' => 7, 'sap_location_name' => 'Meggitt (OC), Inc', 'name' => 'Orange County', 'plant_code' => 3212],
            ['timezone' => 'America/Los_Angeles', 'id' => 8, 'sap_location_name' => 'Meggitt Safety Systems', 'name' => 'Ventura County', 'plant_code' => 3126],
            ['timezone' => 'America/Detroit', 'id' => 9, 'sap_location_name' => 'MCS Troy', 'name' => 'Troy', 'plant_code' => 3115],
            ['timezone' => 'Europe/London', 'id' => 10, 'sap_location_name' => 'MCS Birmingham', 'name' => 'Birmingham', 'plant_code' => 1116],
            ['timezone' => 'Europe/London', 'id' => 11, 'sap_location_name' => 'MCS Coventry', 'name' => 'Coventry', 'plant_code' => 1101],
            ['timezone' => 'Europe/Paris', 'id' => 12, 'sap_location_name' => 'AEVA SAS', 'name' => 'AEVA SAS', 'plant_code' => 2210],
            ['timezone' => 'America/New_York', 'id' => 13, 'sap_location_name' => 'MABS Akron', 'name' => 'MABS Akron', 'plant_code' => 3006],
            ['timezone' => 'Europe/London', 'id' => 14, 'sap_location_name' => 'MABS UK', 'name' => 'MABS UK', 'plant_code' => 1022],
            ['timezone' => 'Europe/London', 'id' => 15, 'sap_location_name' => 'MCS Dunstable', 'name' => 'Dunstable', 'plant_code' => 1130],
            ['timezone' => 'America/Los_Angeles', 'id' => 16, 'sap_location_name' => 'MCS San Diego', 'name' => 'San Diego', 'plant_code' => 3325],
            ['timezone' => 'America/New_York', 'id' => 17, 'sap_location_name' => 'Meggitt (Rockmart), Inc.', 'name' => 'Rockmart', 'plant_code' => 3110],
            ['timezone' => 'Europe/Paris', 'id' => 18, 'sap_location_name' => 'Meggitt (Sensorex) SAS', 'name' => 'Sensorex', 'plant_code' => 2205],
            ['timezone' => 'Europe/London', 'id' => 19, 'sap_location_name' => 'Meggitt Aerospace Limited', 'name' => 'Loughbrough', 'plant_code' => 1110],
            ['timezone' => 'Europe/London', 'id' => 20, 'sap_location_name' => 'MSS UK', 'name' => 'Basingstoke', 'plant_code' => 1205],
            ['timezone' => 'Europe/London', 'id' => 21, 'sap_location_name' => 'S&S Coventry', 'name' => 'S&S Coventry', 'plant_code' => 1422],
            ['timezone' => 'Europe/London', 'id' => 22, 'sap_location_name' => 'S&S Miami', 'name' => 'Miami', 'plant_code' => 3515]
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}