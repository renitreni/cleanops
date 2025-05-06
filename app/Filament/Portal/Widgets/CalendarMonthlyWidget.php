<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;

class CalendarMonthlyWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Complaints';

    // protected int | string | array $columnSpan = 'full';

    protected function getMaxHeight(): ?string
    {
        return '100';
    }

    protected function getData(): array
    {
        // Fetch all observations within the last 3 months grouped by week and status
        $observations = Observation::selectRaw('DATE_FORMAT(created_at, \'%Y-%m\')  as month, status, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'status')
            ->orderBy('month', 'desc')
            ->get();

        // Initialize dataset structure
        $labels = [];
        $data = [
            'total' => [],
            'pending' => [],
            'in_progress' => [],
            'rejected' => [],
            'resolved' => [],
        ];

        // Process the results into a structured array
        foreach ($observations as $observation) {
            $month = $observation->month;
            if (! isset($labels[$month])) {
                $labels[$month] = $month;
                $data['total'][$month] = 0;
                $data['pending'][$month] = 0;
                $data['in_progress'][$month] = 0;
                $data['rejected'][$month] = 0;
                $data['resolved'][$month] = 0;
            }
            $data['total'][$month] += $observation->total;
            $data[$observation->status][$month] = $observation->total;
        }

        // Ensure data arrays align with labels
        $months = array_values($labels);
        foreach ($data as $key => &$values) {
            $values = array_values(array_replace(array_fill_keys($months, 0), $values));
        }

        // Return structured dataset
        return [
            'datasets' => [
                ['label' => 'Total', 'data' => $data['total'], 'borderColor' => 'black'],
                ['label' => 'Pending', 'data' => $data['pending'], 'borderColor' => 'rgba(229,198,34,1)'],
                ['label' => 'In Progress', 'data' => $data['in_progress'], 'borderColor' => 'blue'],
                ['label' => 'Resolved', 'data' => $data['resolved'], 'borderColor' => 'green'],
                ['label' => 'Rejected', 'data' => $data['rejected'], 'borderColor' => 'red'],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
