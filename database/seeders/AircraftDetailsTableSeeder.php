<?php

namespace Database\Seeders;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class AircraftDetailsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'aircraft_details';
		$this->insert_chunk_size = 300;
		$this->filename = base_path().'/database/seeders/csvs/aircraft_detail/aircraft-details-may-2021.csv';
		$this->offset_rows = 1;
		$this->mapping = [
    		0 => 'aircraft_fully_qualified_registration_no',
            1 => 'aircraft_identification_no',
            2 => 'manufacturer_name',
            3 => 'manufacturer_code',
            4 => 'aircraft_model_identifier',
            5 => 'aircraft_series_identifier',
            6 => 'engine_manufacturer',
            7 => 'engine_manufacturer_code',
            8 => 'engine_type',
            9 => 'engines_series',
            
		];
		$this->should_trim = true;
	}
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

		parent::run();
	}
}
