<?php

namespace Database\Factories;

use App\Models\MedicalStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalStaff>
 */
class MedicalStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => MedicalStaff::TYPE_DOCTOR,
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->firstName(),
            'position' => 'лікар загальної практики - сімейний лікар',
            'employment_status' => 'основний працівник',
            'position_status' => 'зайнята',
            'ambulatoriya_id' => null,
            'is_active' => true,
        ];
    }

    public function doctor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => MedicalStaff::TYPE_DOCTOR,
            'position' => 'лікар загальної практики - сімейний лікар',
        ]);
    }

    public function nurse(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => MedicalStaff::TYPE_NURSE,
            'position' => 'сестра медична загальної практики - сімейна медицина',
        ]);
    }
}
