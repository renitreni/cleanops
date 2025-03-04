<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'observation_id',
        'contractor_id',
        'assigned_by', // (user_id)
        'status', // (assigned, completed, rejected)
        'completion_photo',
        'completed_at',
    ];

    public function observation()
    {
        return $this->belongsTo(Observation::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
