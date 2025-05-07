<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'observation_id',
        'contractor_id',
        'assigned_by', // (user_id)
        'status', // (assigned, completed, rejected)
        'completion_photo',
        'completed_at',
        'assigned_at',
        'completed_at',
        'rejected_at'
    ];

    protected static function booted(): void
    {
        static::updated(function (Task $task) {
            $changeValues = $task->getChanges();
            if(isset($changeValues['status'])) {
                $statusField = $changeValues['status'] . '_at';
                $task->updateQuietly([
                    $statusField => now()
                ]);
            }
        });
    }

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

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
