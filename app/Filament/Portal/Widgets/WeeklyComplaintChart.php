<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;

class WeeklyComplaintChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Complaints';

    protected int | string | array $columnSpan = 'full';

    protected function getMaxHeight(): ?string
    {
        return '100';
    }

    protected function getData(): array
    {
        $observations = Observation::selectRaw('YEARWEEK(created_at, 1) as week, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths('3'))
            ->groupBy('week')
            ->orderBy('week', 'desc');

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $observations->get()->pluck('total')->toArray(),
                    'borderColor' => 'black',
                ],
                [
                    'label' => 'Pending',
                    'data' => $observations->where('status', 'pending')->get()->pluck('total')->toArray(),
                    'borderColor' => 'rgba(229,198,34,1)',
                ],
                [
                    'label' => 'In Progress',
                    'data' => $observations->where('status', 'in_progress')->get()->pluck('total')->toArray(),
                    'borderColor' => 'blue',
                ],
            ],
            'labels' => $observations->pluck('week')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
