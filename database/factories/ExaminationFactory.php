<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Examination>
 */
class ExaminationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(10, true),
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'time_limit' => fake()->numberBetween(60, 60 * 2),
            'published_at' => now()->format('Y-m-d h:i:s'),
        ];
    }
}
