<?php

namespace App\Imports;

use App\UtasCode;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class UtasCodeImport implements ToModel, WithBatchInserts, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new UtasCode([
            'PLANT' => $row['PLANT'],
            'MATNR' => $row['MATNR'],
            'SUB' => $row['SUB'], // ALL CAPS. NO Blank Lines. Spaces OK. Max Characters = 40
            'COMP' => $row['COMP'], // 1st Character CAP. NO Blank Lines. Spaces OK. Max Characters = 40
            'FEAT' => $row['FEAT'], // no caps. Blank Lines are OK. Spaces OK. Max Characters = 40
            'DESCR' => $row['DESCR'], // no caps. Blanks filled with a ".". Spaces OK. Max Characters = 40
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
            'MATNR' => 'required',
            'SUB' => 'required|max:40',
            'COMP' => 'required|max:40',
            'FEAT' => 'nullable|max:40',
            'DESCR' => 'required|max:40'
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
        $data['SUB'] = strtoupper($data['SUB']);
        $data['COMP'] = ucfirst($data['COMP']);
        $data['FEAT'] = strtolower($data['FEAT']);
        $data['DESCR'] = empty($data['DESCR']) ? '.' : strtolower($data['DESCR']);
        
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
