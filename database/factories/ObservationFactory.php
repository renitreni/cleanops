<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'contact_no' => $this->faker->phoneNumber(),
            'description' => $this->faker->sentence(),
            'photo' => json_encode([[$this->faker->imageUrl()], [$this->faker->imageUrl()]]),
            'location' => json_encode([
                'lat' => $this->faker->latitude(),
                'lng' => $this->faker->longitude(),
            ]),
            'reported_by' => fake()->swiftBicNumber(),
            'status' => 'pending', // 'pending', 'in_progress', 'resolved'
        ];
    }
}
