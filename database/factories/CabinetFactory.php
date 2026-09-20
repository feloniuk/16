<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Cabinet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cabinet>
 */
class CabinetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'number' => (string) fake()->unique()->numberBetween(1, 500),
            'ambulatoriya_id' => null,
            'notes' => null,
            'is_active' => true,
        ];
    }
}
