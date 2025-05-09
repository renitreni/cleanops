<?php

namespace App\Exports\Sheet;

use App\Models\Observation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
    public function __construct(private string $status, private array $dateRange) {}

    public function map($row): array
    {
        return [
            $row->serial,
            $row->name,
            $row->description,
            $row->contact_no,
            $row->email,
            Carbon::parse($row->created_at)->format('F j, Y'),
            $this->diffForHumansDuration($row->pending_at,$row->resolved_at),
        ];
    }

    public function diffForHumansDuration($dateParam1, $dateParam2)
    {
        $diff = '-';
        if ($dateParam2) {
            $date1 = \Carbon\Carbon::parse($dateParam1);
            $date2 = \Carbon\Carbon::parse($dateParam2);

            $diff = $date1->diff($date2);
        }

        return $diff;
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
            'G' => 40,
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
            'duration (pending -> resolved)',
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
                'pending_at',
                'resolved_at',
                'in_progress_at',
                // duration is NULL if resolve_at IS NULL, else the diff in seconds
                DB::raw(<<<'SQL'
                    CASE
                      WHEN resolved_at IS NULL THEN NULL
                      ELSE TIMESTAMPDIFF(SECOND, pending_at, resolved_at)
                    END as duration
                SQL)
            ])
            ->when($this->dateRange['from'], function ($q) {
                $q->whereBetween('created_at', [$this->dateRange['from'], $this->dateRange['until']]);
            })
            ->where('status', $this->status)
            ->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return $this->status;
    }
}
