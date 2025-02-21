<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    /** @use HasFactory<\Database\Factories\ObservationFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'description',
        'photo',
        'location', // (lat, lng) 
        'reported_by', // (user_id) 
        'status', // (pending, in_progress, resolved) 
    ];

    protected $casts = [
        'location' => 'array',
    ];

    // Relationships
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function task()
    {
        return $this->hasOne(Task::class);
    }
}
