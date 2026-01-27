<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkLog>
 */
class WorkLogFactory extends Factory
{
    protected $model = WorkLog::class;

    public function definition(): array
    {
        $workType = fake()->randomElement([
            'inventory_transfer',
            'cartridge_replacement',
            'repair_sent',
            'repair_returned',
            'manual',
        ]);

        return [
            'work_type' => $workType,
            'description' => $this->generateDescription($workType),
            'branch_id' => Branch::where('is_active', true)->inRandomOrder()->first()?->id,
            'room_number' => (string) fake()->numberBetween(1, 999),
            'performed_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'user_id' => User::where('is_active', true)->inRandomOrder()->first()?->id ?? 1,
            'notes' => fake()->optional(0.3)->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateDescription(string $workType): string
    {
        return match ($workType) {
            'inventory_transfer' => 'Переміщення '.fake()->word().': каб. '.fake()->numberBetween(1, 999),
            'cartridge_replacement' => 'Заміна картриджа HP '.fake()->bothify('???###'),
            'repair_sent' => 'Відправка на ремонт: '.fake()->sentence(),
            'repair_returned' => 'Повернення з ремонту: '.fake()->sentence(),
            'manual' => fake()->sentence(),
        };
    }

    public function inventoryTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'work_type' => 'inventory_transfer',
        ]);
    }

    public function cartridgeReplacement(): static
    {
        return $this->state(fn (array $attributes) => [
            'work_type' => 'cartridge_replacement',
        ]);
    }
}
