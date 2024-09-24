<?php

namespace Database\Seeders;

use App\UtasCode;
use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class UpdatedUtasPartNumbersTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'utas_part_numbers';
		$this->insert_chunk_size = 300;
		$this->filename = base_path().'/database/seeders/csvs/utas_codes_11_07_2022/NoHo_Products_utas_part_numbers.csv';
		$this->offset_rows = 1;
		$this->mapping = [
            1 => 'meggitt_part_no',
            2 => 'utas_part_no',
            3 => 'description'
		];
		$this->should_trim = true;
		$this->timestamps = false;
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