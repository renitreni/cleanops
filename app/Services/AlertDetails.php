<?php

namespace App\Services;

use App\Models\Observation;
use App\Models\Task;
use Carbon\Carbon;

class AlertDetails
{
    public function pending()
    {
        return Observation::where('status', 'pending')->count();
    }

    public function inProgress()
    {
        return Observation::where('status', 'in_progress')->count();
    }

    public function due()
    {
        return Task::query()->where('updated_at', '<', Carbon::now()->subDay())->count();
    }
}
