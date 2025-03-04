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
        'serial',
        'description',
        'name',
        'contact_no',
        'email',
        'photo',
        'location', // (lat, lng)
        'reported_by', // (user_id)
        'status', // (pending, in_progress, resolved, rejected)
    ];

    protected $casts = [
        'location' => 'array',
    ];

    protected static function booting(): void
    {
        static::creating(function ($observation) {
            $observation->serial = now()->format('YmdHis');
        });
    }

    public function task()
    {
        return $this->hasOne(Task::class);
    }
}
