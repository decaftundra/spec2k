<?php

namespace App\Imports;

use App\UtasReasonCode;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class UtasReasonCodeImport implements ToModel, WithBatchInserts, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new UtasReasonCode([
            'PLANT' => $row['PLANT'],
            'TYPE' => $row['TYPE'],
            'REASON' => $row['REASON']
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
            'PLANT' => 'required|numeric',
            'TYPE' => 'required|max:1|in:U,S',
            'REASON' => 'required|max:40'
        ];
    }
    
    /**
     * Perform any data manipulation before validation.
     *
     * @params array $data
     * @params integer $index
     * @return array $data
     */
    public function prepareForValidation($data, $index)
    {
        $data['REASON'] = strtoupper($data['REASON']);
        
        return $data;
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
