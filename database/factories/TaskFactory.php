<?php

namespace Database\Factories;

use App\Models\Contractor;
use App\Models\Observation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'observation_id' => Observation::factory(),
            'contractor_id' => Contractor::factory(),
            'assigned_by' => User::factory(),
            'status' => 'assigned',
            'completion_photo' => null,
            'completed_at' => null,
        ];
    }
}
