<?php

namespace Database\Seeders;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EngineDetailsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'engine_details';
		$this->insert_chunk_size = 300;
		$this->filename = base_path().'/database/seeders/csvs/engine_detail/EngineData_reference_table_080621.csv';
		$this->offset_rows = 1;
		$this->mapping = [
    		0 => 'engine_manufacturer',
            1 => 'engine_manufacturer_code',
            2 => 'engine_type',
            3 => 'engines_series'
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
