<?php

namespace Database\Seeders;
    
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UtasReasonCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Schema::disableForeignKeyConstraints();
		
		DB::table('utas_reason_codes')->truncate();
		
		DB::table('utas_reason_codes')->insert([
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'AIR SYSTEM FAULT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'BOWED ROTOR PROTECTION'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'CAUSED FOD IN ENGINE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'DISAG FAULT MSG'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'DURING DESCENT FAILURE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ECAM MSG'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'EEC MAINT BIT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ELECTRICAL FAILURE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ENGINE AIR SYSTEM FAULT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ENGINE STALLS'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ENGINE START ABORT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ENGINE SURGE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'ENGINE WILL NOT START'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'EXTERNAL COMPONENT DAMAGED'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'EXTERNAL COMPONENT MISSING'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'EXTERNAL LEAKAGE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'EXTERNAL VISUAL'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'FAULT MESSAGE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'INOPERATIVE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'INTERMITTENT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'INTERMITTENT LIGHTS'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'INTERNAL LEAKAGE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'KEEPS RUNNING IN ONE DIRECTION'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'LIGHT INDICATION FAULT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'NO START'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'NON OPERATIVE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'OVERCOOLING'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'OVERHEATING'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'REPAIR'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'REPEAT FAULT'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'SAFETY WIRE BROKEN'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'SEIZED'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'SLOW TO MOVE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'STUCK CLOSE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'STUCK MID POSITION'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'STUCK OPEN'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'WILL NOT CLOSE'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'WILL NOT OPEN'],
            ['PLANT' => 3101, 'TYPE' => 'U', 'REASON' => 'external visual'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'CORE'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'EMBODY SERVICE BULLETIN'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'EXCHANGE UNIT'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'FOR CLEANING'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'INSPECT'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'INVESTIGATION UNIT'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'MODIFICATION'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'NO FAILURE OCCURRED'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'OVERHAUL'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'RECERTIFY'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'SERVICE BULLETIN'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'SHELF LIFE EXPIRED'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'TEST'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'TEST AND EVALUATE'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'TIME UP'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'UNKNOWN'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'UPGRADE'],
            ['PLANT' => 3101, 'TYPE' => 'S', 'REASON' => 'UPGRADE CONFIGURATION']
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}