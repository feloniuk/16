<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehousePriorityTest extends TestCase
{
    use RefreshDatabase;

    private const WAREHOUSE_BRANCH_ID = 6;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function ensureWarehouseBranch(): void
    {
        if (! Branch::find(self::WAREHOUSE_BRANCH_ID)) {
            Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        }
    }

    private function makeWarehouseItem(string $equipmentType, bool $isPriority = false): RoomInventory
    {
        $this->ensureWarehouseBranch();

        return RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => 'Склад',
            'equipment_type' => $equipmentType,
            'quantity' => 5,
            'unit' => 'шт',
            'is_priority' => $isPriority,
        ]);
    }

    public function test_toggle_priority_marks_all_rows_with_same_equipment_type(): void
    {
        $admin = $this->makeAdmin();
        $itemOne = $this->makeWarehouseItem('Папір А4');
        $this->ensureWarehouseBranch();
        $itemTwo = RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => 'Склад',
            'equipment_type' => 'Папір А4',
            'quantity' => 3,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->postJson(route('warehouse.toggle-priority'), [
            'equipment_type' => 'Папір А4',
            'is_priority' => true,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertTrue($itemOne->fresh()->is_priority);
        $this->assertTrue($itemTwo->fresh()->is_priority);
    }

    public function test_toggle_priority_can_unset(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeWarehouseItem('Ручка кулькова', isPriority: true);

        $response = $this->actingAs($admin)->postJson(route('warehouse.toggle-priority'), [
            'equipment_type' => 'Ручка кулькова',
            'is_priority' => false,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertFalse($item->fresh()->is_priority);
    }

    public function test_toggle_priority_requires_authorized_role(): void
    {
        $director = User::factory()->create(['role' => 'director', 'is_active' => true]);
        $this->makeWarehouseItem('Степлер');

        $response = $this->actingAs($director)->postJson(route('warehouse.toggle-priority'), [
            'equipment_type' => 'Степлер',
            'is_priority' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_index_orders_priority_items_first(): void
    {
        $admin = $this->makeAdmin();
        $this->makeWarehouseItem('Аааа звичайний товар');
        $this->makeWarehouseItem('Яааа пріоритетний товар', isPriority: true);

        $response = $this->actingAs($admin)->get(route('warehouse.index', ['category' => 'all']));

        $response->assertStatus(200);
        $items = $response->viewData('items');

        $this->assertSame('Яааа пріоритетний товар', $items->first()->equipment_type);
    }
}
