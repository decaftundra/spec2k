<?php

namespace App\Exports;

use App\UtasReasonCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UtasReasonCodeExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    public function headings(): array
    {
        return [
            'PLANT',
            'TYPE',
            'REASON'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UtasReasonCode::all()->makeHidden(['id', 'created_at', 'updated_at']);
    }
}
