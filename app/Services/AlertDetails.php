<?php

namespace App\Services;

use App\Models\Observation;

class AlertDetails
{
    public function pending()
    {
        return Observation::where('status', 'pending')->count();
    }
}
