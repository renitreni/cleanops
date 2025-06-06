<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    /** @use HasFactory<\Database\Factories\ObservationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'serial',
        'description',
        'name',
        'contact_no',
        'email',
        'photo',
        'location', // (lat, lng)
        'reported_by', // (user_id)
        'status', // (pending, in_progress, resolved, rejected)
        'pending_at',
        'in_progress_at',
        'resolved_at',
        'rejected_at'
    ];

    protected $casts = [
        'location' => 'array',
    ];

    protected static function booted(): void
    {
        static::updated(function (Observation $task) {
            $changeValues = $task->getChanges();
            if(isset($changeValues['status'])) {
                $statusField = $changeValues['status'] . '_at';
                $task->updateQuietly([
                    $statusField => now()
                ]);
            }
        });
    }

    protected static function booting(): void
    {
        static::creating(function ($observation) {
            if (app()->environment('local')) {
                $observation->serial = now()->format('YmdHis') + fake()->randomDigit();
            } else {
                $observation->serial = now()->format('YmdHis');
            }
        });
    }

    public function task()
    {
        return $this->hasOne(Task::class);
    }
}
