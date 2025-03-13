<?php

namespace App\Exports\Sheet;

use App\Models\Observation;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintStatusSheet implements FromQuery, WithColumnFormatting, WithColumnWidths, WithHeadings, WithStyles, WithTitle, WithMapping
{
    public function __construct(private string $status) {}

    public function map($row): array
    {
        return [
            $row->serial,
            $row->name,
            $row->description,
            $row->contact_no,
            $row->email,
            Carbon::parse($row->created_at)->format('F j, Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 30,
        ];
    }

    public function headings(): array
    {
        return [
            'serial',
            'name',
            'description',
            'contact_no',
            'email',
            'created_at',
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return Observation::query()
            ->select([
                'serial',
                'name',
                'description',
                'contact_no',
                'email',
                'created_at',
            ])
            ->where('status', $this->status)
            ->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return $this->status;
    }
}
