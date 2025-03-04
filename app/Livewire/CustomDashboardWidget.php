<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class CustomDashboardWidget extends Widget
{
    protected static string $view = 'livewire.custom-dashboard-widget';

    protected int|string|array $columnSpan = 'full';
}
