<?php

namespace Database\Seeders;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class AdditionalUtasPartsSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'utas_codes';
		$this->insert_chunk_size = 300;
		$this->filename = base_path().'/database/seeders/csvs/utas_codes/additional-collins-parts-28-10-2021.csv';
		$this->offset_rows = 1;
		$this->mapping = [
    		0 => 'PLANT',
            1 => 'MATNR',
            2 => 'SUB',
            3 => 'COMP',
            4 => 'FEAT',
            5 => 'DESCR',
            
		];
		$this->should_trim = true;
		$this->timestamps = true;
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
		// DB::table($this->table)->truncate();

		parent::run();
    }
}
