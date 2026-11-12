<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\User;
use App\Models\WarehouseMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseMovementsBalanceTest extends TestCase
{
    use RefreshDatabase;

    private const WAREHOUSE_BRANCH_ID = 6;

    private function warehouseItem(string $inventoryNumber, int $quantity): RoomInventory
    {
        return RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => 'Склад',
            'equipment_type' => 'Папір А4',
            'inventory_number' => $inventoryNumber,
            'quantity' => $quantity,
            'unit' => 'пач',
        ]);
    }

    private function movement(User $user, RoomInventory $item, string $type, int $quantity, int $balanceAfter, string $date): WarehouseMovement
    {
        return WarehouseMovement::create([
            'user_id' => $user->id,
            'inventory_id' => $item->id,
            'type' => $type,
            'quantity' => $quantity,
            'balance_after' => $balanceAfter,
            'operation_date' => $date,
        ]);
    }

    public function test_movements_history_shows_the_balance_recorded_for_each_operation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        $paper = $this->warehouseItem('W-1', 4);
        $this->warehouseItem('W-2', 100);

        $this->movement($admin, $paper, 'receipt', 17, 17, '2026-11-01');
        $this->movement($admin, $paper, 'issue', 6, 11, '2026-11-02');
        $this->movement($admin, $paper, 'issue', 7, 4, '2026-11-03');

        $response = $this->actingAs($admin)->get(route('warehouse.movements'));

        $response->assertOk();
        foreach (['17 пач', '11 пач', '4 пач'] as $balance) {
            $response->assertSee($balance);
        }
        $response->assertDontSee('104 пач');
    }

    public function test_item_card_and_movements_history_agree_on_balances(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        $paper = $this->warehouseItem('W-1', 12);
        $this->warehouseItem('W-2', 100);
        $this->movement($admin, $paper, 'inventory', 3, 12, '2026-11-04');

        $this->actingAs($admin)->get(route('warehouse.show', $paper))
            ->assertOk()
            ->assertSee('<span class="badge bg-info">12</span>', false)
            ->assertSee('+3');

        $this->actingAs($admin)->get(route('warehouse.movements'))
            ->assertOk()
            ->assertSee('12 пач')
            ->assertSee('+3')
            ->assertDontSee('112 пач');
    }
}
