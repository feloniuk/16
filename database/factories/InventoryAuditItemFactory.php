<?php

namespace Database\Factories;

use App\Models\InventoryAuditItem;
use App\Models\InventoryAuditRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryAuditItem>
 */
class InventoryAuditItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_audit_room_id' => InventoryAuditRoom::factory(),
            'room_inventory_id' => null,
            'equipment_type' => fake()->randomElement(['Монітор', 'Системний блок', 'Принтер', 'Клавіатура']),
            'brand' => null,
            'model' => null,
            'serial_number' => null,
            'inventory_number' => null,
            'quantity' => 1,
            'notes' => null,
            'resolution' => null,
        ];
    }
}
