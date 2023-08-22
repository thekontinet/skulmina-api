<?php

namespace Database\Factories;

use App\Models\Examination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'examination_id' => Examination::factory(),
            'start_at' => now()->format('Y-m-d h:i:s'),
            'end_at' => now()->addHours(2)->format('Y-m-d h:i:s'),
        ];
    }
}
