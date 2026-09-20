<?php

namespace Database\Factories;

use App\Models\Cabinet;
use App\Models\CabinetShift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CabinetShift>
 */
class CabinetShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cabinet_id' => Cabinet::factory(),
            'shift' => CabinetShift::SHIFT_FIRST,
            'doctor_id' => null,
            'nurse_id' => null,
        ];
    }
}
