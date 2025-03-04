<?php

namespace App\Livewire;

use App\Models\Task;
use Filament\Forms\Components\Section;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidgets extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Task', Task::count())->chartColor('red'),
            Stat::make('Assigned Task', Task::where('status', 'assigned')->count())
                ->color('info'),
            Stat::make('Completed Task', Task::where('status', 'completed')->count())
                ->color('success'),
            Stat::make('Rejected Task', Task::where('status', 'rejected')->count())
                ->color('danger'),
        ];
    }
}
