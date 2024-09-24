<?php

namespace App\Imports;

use App\UtasPartNumber;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class UtasPartNumberImport implements ToModel, WithBatchInserts, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new UtasPartNumber([
            'meggitt_part_no' => $row['meggitt_part_no'],
            'utas_part_no' => $row['utas_part_no'],
            'description' => $row['description']
        ]);
    }
    
    /**
     * Validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'meggitt_part_no' => 'required',
            'utas_part_no' => 'required',
            'description' => 'required'
        ];
    }
    
    /**
     * Set the chunk size.
     *
     * @return integer
     */
    public function chunkSize(): int
    {
        return 300;
    }
    
    /**
     * Set the batch size.
     *
     * @return integer
     */
    public function batchSize(): int
    {
        return 1000;
    }
}
