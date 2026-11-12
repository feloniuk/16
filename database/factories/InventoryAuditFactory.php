<?php

namespace Database\Factories;

use App\Models\InventoryAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryAudit>
 */
class InventoryAuditFactory extends Factory
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
            'title' => 'Обхід '.fake()->date('d.m.Y'),
            'status' => InventoryAudit::STATUS_IN_PROGRESS,
            'notes' => null,
            'applied_at' => null,
        ];
    }

    public function applied(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => InventoryAudit::STATUS_APPLIED,
            'applied_at' => now(),
        ]);
    }
}
