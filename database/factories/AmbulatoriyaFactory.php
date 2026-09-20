<?php

namespace Database\Factories;

use App\Models\Ambulatoriya;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ambulatoriya>
 */
class AmbulatoriyaFactory extends Factory
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
            'name' => 'Амбулаторія № '.fake()->unique()->numberBetween(1, 100),
        ];
    }
}
