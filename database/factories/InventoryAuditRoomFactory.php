<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\InventoryAudit;
use App\Models\InventoryAuditRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryAuditRoom>
 */
class InventoryAuditRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_audit_id' => InventoryAudit::factory(),
            'branch_id' => Branch::factory(),
            'room_number' => (string) fake()->unique()->numberBetween(1, 500),
            'cabinet_id' => null,
            'notes' => null,
        ];
    }
}
