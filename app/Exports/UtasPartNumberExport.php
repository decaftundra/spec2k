<?php

namespace App\Exports;

use App\UtasPartNumber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UtasPartNumberExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    public function headings(): array
    {
        return [
            'meggitt_part_no',
            'utas_part_no',
            'description'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UtasPartNumber::all()->makeHidden(['id', 'created_at', 'updated_at']);;
    }
}
