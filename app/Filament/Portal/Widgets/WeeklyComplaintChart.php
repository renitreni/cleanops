<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;

class WeeklyComplaintChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Complaints';

   // protected int | string | array $columnSpan = 'full';

    protected function getMaxHeight(): ?string
    {
        return '100';
    }
    protected function getData(): array
    {
        // Fetch all observations within the last 3 months grouped by week and status
        $observations = Observation::selectRaw('YEARWEEK(created_at, 1) as week, status, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(3))
            ->groupBy('week', 'status')
            ->orderBy('week', 'desc')
            ->get();
    
        // Initialize dataset structure
        $labels = [];
        $data = [
            'total' => [],
            'pending' => [],
            'in_progress' => [],
            'rejected' => [],
            'resolved' => []
        ];
    
        // Process the results into a structured array
        foreach ($observations as $observation) {
            $week = $observation->week;
            if (!isset($labels[$week])) {
                $labels[$week] = $week;
                $data['total'][$week] = 0;
                $data['pending'][$week] = 0;
                $data['in_progress'][$week] = 0;
                $data['rejected'][$week] = 0;
                $data['resolved'][$week] = 0;
            }
            $data['total'][$week] += $observation->total;
            $data[$observation->status][$week] = $observation->total;
        }
    
        // Ensure data arrays align with labels
        $weeks = array_values($labels);
        foreach ($data as $key => &$values) {
            $values = array_values(array_replace(array_fill_keys($weeks, 0), $values));
        }
    
        // Return structured dataset
        return [
            'datasets' => [
                ['label' => 'Total', 'data' => $data['total'], 'borderColor' => 'black'],
                ['label' => 'Pending', 'data' => $data['pending'], 'borderColor' => 'rgba(229,198,34,1)'],
                ['label' => 'In Progress', 'data' => $data['in_progress'], 'borderColor' => 'blue'],
                ['label' => 'Rejected', 'data' => $data['rejected'], 'borderColor' => 'red'],
                ['label' => 'Resolved', 'data' => $data['resolved'], 'borderColor' => 'green'],
            ],
            'labels' => $weeks,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
