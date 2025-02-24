<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\Observation;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create users
        User::factory()->count(10)->create();

        // Create contractors
        Contractor::factory()->count(5)->create();

        // Create observations
        Observation::factory()->count(20)->create();

        // Assign tasks to contractors based on observations
        Observation::all()->each(function ($observation) {
            Task::factory()->create([
                'observation_id' => $observation->id,
                'contractor_id' => Contractor::inRandomOrder()->first()->id,
                'assigned_by' => User::where('role', 'admin')->inRandomOrder()->first()->id ?? User::first()->id,
            ]);
        });

        Observation::query()->update(['photo' => 'https://dummyimage.com/640x4:3/']);
    }
}
