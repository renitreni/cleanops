<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'task_id',
        'reviewer_id', // (user_id)
        'rating', // (1-5)
        'comments',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
