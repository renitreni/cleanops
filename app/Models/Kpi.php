<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    /** @use HasFactory<\Database\Factories\KpiFactory> */
    use HasFactory;

    protected $fillable = [
        'id', 
        'contractor_id', 
        'metric_name', 
        'value', 
        'date', 
    ];
}
