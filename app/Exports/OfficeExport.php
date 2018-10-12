<?php

namespace App\Exports;

use App\Subdivision;
use Maatwebsite\Excel\Concerns\FromCollection;

class OfficeExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Subdivision::all();
    }
}
