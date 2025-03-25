<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplainResolve extends Model
{
    /** @use HasFactory<\Database\Factories\ComplainResolveFactory> */
    use HasFactory;

    protected $fillable = [
        'serial',
        'evidences',
        'approved_by',
    ];
}
