<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    /** @use HasFactory<\Database\Factories\ContractorFactory> */
    use HasFactory;    
    
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
}
