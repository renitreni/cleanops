<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    /** @use HasFactory<\Database\Factories\ContractorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'contact_person',
        'phone',
        'email',
        'status', // (active, inactive)
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'entity_id')->where('role', 'contractor');
    }
}
