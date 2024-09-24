<?php

namespace App\Exports;

use App\UtasCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UtasCodeExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    public function headings(): array
    {
        return [
            'PLANT',
            'MATNR',
            'SUB',
            'COMP',
            'FEAT',
            'DESCR'
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UtasCode::all()->makeHidden(['id', 'created_at', 'updated_at']);
    }
}
