<?php

namespace App\Exports;

use App\Exports\Sheet\ComplaintStatusSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ComplaintReport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
        $sheets = [];
        foreach ($statuses as $value) {
            $sheets[] = new ComplaintStatusSheet($value);
        }

        return $sheets;
    }
}
