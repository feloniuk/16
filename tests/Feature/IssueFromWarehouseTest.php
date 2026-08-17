<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Models\RoomInventory;
use App\Models\User;
use App\Models\WarehouseMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueFromWarehouseTest extends TestCase
{
    use RefreshDatabase;

    private const WAREHOUSE_BRANCH_ID = 6;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function makeWarehouseKeeper(): User
    {
        return User::factory()->create(['role' => 'warehouse_keeper', 'is_active' => true]);
    }

    private function ensureWarehouseBranch(): void
    {
        if (! Branch::find(self::WAREHOUSE_BRANCH_ID)) {
            Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        }
    }

    private function makeInventoryItem(int $quantity = 10, string $equipmentType = 'Картридж'): RoomInventory
    {
        $branch = Branch::factory()->create();

        return RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '1',
            'equipment_type' => $equipmentType,
            'full_name' => 'Картридж HP 85A',
            'quantity' => $quantity,
            'unit' => 'шт',
        ]);
    }

    private function makeWarehouseItem(int $quantity = 10, string $equipmentType = 'Папір А4'): RoomInventory
    {
        $this->ensureWarehouseBranch();

        return RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => 'Склад',
            'equipment_type' => $equipmentType,
            'quantity' => $quantity,
            'unit' => 'шт',
        ]);
    }

    private function makeRepair(): RepairRequest
    {
        $branch = Branch::factory()->create();

        return RepairRequest::create([
            'user_telegram_id' => 12345,
            'branch_id' => $branch->id,
            'room_number' => '101',
            'description' => 'Тестова заявка',
            'status' => 'нова',
        ]);
    }

    private function makeCartridge(): CartridgeReplacement
    {
        $branch = Branch::factory()->create();

        return CartridgeReplacement::create([
            'user_telegram_id' => 12345,
            'branch_id' => $branch->id,
            'room_number' => '101',
            'printer_info' => 'HP LaserJet',
            'cartridge_type' => 'HP 85A',
            'replacement_date' => today()->toDateString(),
        ]);
    }

    // --- Repair tests ---

    public function test_admin_can_issue_from_warehouse_for_repair(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $repair = $this->makeRepair();

        $response = $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 2]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_issuing_for_repair_creates_warehouse_movement(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $repair = $this->makeRepair();

        $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 3]],
            'operation_date' => today()->toDateString(),
            'note' => 'Деталь',
        ]);

        $this->assertDatabaseHas('warehouse_movements', [
            'inventory_id' => $item->id,
            'type' => 'issue',
            'quantity' => -3,
            'balance_after' => 7,
            'user_id' => $admin->id,
        ]);
    }

    public function test_issuing_for_repair_decreases_inventory_quantity(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $repair = $this->makeRepair();

        $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 4]],
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertEquals(6, $item->fresh()->quantity);
    }

    public function test_can_issue_multiple_items_for_repair_in_one_request(): void
    {
        $admin = $this->makeAdmin();
        $itemOne = $this->makeInventoryItem(10, 'Тонер');
        $itemTwo = $this->makeInventoryItem(5, 'Папір');
        $repair = $this->makeRepair();

        $response = $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [
                ['inventory_id' => $itemOne->id, 'quantity' => 2],
                ['inventory_id' => $itemTwo->id, 'quantity' => 1],
            ],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertEquals(8, $itemOne->fresh()->quantity);
        $this->assertEquals(4, $itemTwo->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 2);
    }

    public function test_repair_issue_note_contains_repair_reference(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(5);
        $repair = $this->makeRepair();

        $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $movement = WarehouseMovement::where('inventory_id', $item->id)->first();
        $this->assertStringContainsString("#{$repair->id}", $movement->note);
    }

    public function test_repair_issue_fails_when_insufficient_stock(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(2);
        $repair = $this->makeRepair();

        $response = $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 5]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertEquals(2, $item->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_repair_batch_is_atomic_when_one_item_fails(): void
    {
        $admin = $this->makeAdmin();
        $itemOne = $this->makeInventoryItem(10, 'Тонер');
        $itemTwo = $this->makeInventoryItem(1, 'Папір');
        $repair = $this->makeRepair();

        $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [
                ['inventory_id' => $itemOne->id, 'quantity' => 2],
                ['inventory_id' => $itemTwo->id, 'quantity' => 5],
            ],
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertEquals(10, $itemOne->fresh()->quantity, 'First item should not be decremented if the batch fails');
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_warehouse_keeper_can_issue_for_repair(): void
    {
        $keeper = $this->makeWarehouseKeeper();
        $item = $this->makeInventoryItem(5);
        $repair = $this->makeRepair();

        $response = $this->actingAs($keeper)->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('warehouse_movements', 1);
    }

    public function test_guest_cannot_issue_for_repair(): void
    {
        $item = $this->makeInventoryItem(5);
        $repair = $this->makeRepair();

        $response = $this->post(route('repairs.issue-from-warehouse', $repair), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    // --- Cartridge tests ---

    public function test_admin_can_issue_from_warehouse_for_cartridge(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $cartridge = $this->makeCartridge();

        $response = $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_issuing_for_cartridge_creates_warehouse_movement(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $cartridge = $this->makeCartridge();

        $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 2]],
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertDatabaseHas('warehouse_movements', [
            'inventory_id' => $item->id,
            'type' => 'issue',
            'quantity' => -2,
            'balance_after' => 8,
        ]);
    }

    public function test_issuing_for_cartridge_decreases_inventory_quantity(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(10);
        $cartridge = $this->makeCartridge();

        $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 3]],
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertEquals(7, $item->fresh()->quantity);
    }

    public function test_can_issue_multiple_items_for_cartridge_in_one_request(): void
    {
        $admin = $this->makeAdmin();
        $itemOne = $this->makeInventoryItem(10, 'Картридж HP');
        $itemTwo = $this->makeInventoryItem(5, 'Картридж Canon');
        $cartridge = $this->makeCartridge();

        $response = $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [
                ['inventory_id' => $itemOne->id, 'quantity' => 1],
                ['inventory_id' => $itemTwo->id, 'quantity' => 2],
            ],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertEquals(9, $itemOne->fresh()->quantity);
        $this->assertEquals(3, $itemTwo->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 2);
    }

    public function test_cartridge_issue_note_contains_cartridge_reference(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(5);
        $cartridge = $this->makeCartridge();

        $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $movement = WarehouseMovement::where('inventory_id', $item->id)->first();
        $this->assertStringContainsString("#{$cartridge->id}", $movement->note);
    }

    public function test_cartridge_issue_fails_when_insufficient_stock(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(1);
        $cartridge = $this->makeCartridge();

        $response = $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 5]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertEquals(1, $item->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_guest_cannot_issue_for_cartridge(): void
    {
        $item = $this->makeInventoryItem(5);
        $cartridge = $this->makeCartridge();

        $response = $this->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    // --- Warehouse batch issue tests ---

    public function test_admin_can_batch_issue_multiple_items_to_a_room(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();
        $itemOne = $this->makeWarehouseItem(10, 'Папір А4');
        $itemTwo = $this->makeWarehouseItem(5, 'Ручки');

        $response = $this->actingAs($admin)->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [
                ['inventory_id' => $itemOne->id, 'quantity' => 3],
                ['inventory_id' => $itemTwo->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('warehouse.index'));
        $this->assertEquals(7, $itemOne->fresh()->quantity);
        $this->assertEquals(3, $itemTwo->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 2);
    }

    public function test_batch_issue_fails_atomically_when_one_item_has_insufficient_stock(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();
        $itemOne = $this->makeWarehouseItem(10, 'Папір А4');
        $itemTwo = $this->makeWarehouseItem(1, 'Ручки');

        $response = $this->actingAs($admin)->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [
                ['inventory_id' => $itemOne->id, 'quantity' => 3],
                ['inventory_id' => $itemTwo->id, 'quantity' => 5],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertEquals(10, $itemOne->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_batch_issue_with_replacement_creates_repair_request_for_old_item(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();
        $newItem = $this->makeWarehouseItem(5, 'Принтер HP');
        $oldItem = RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '204',
            'equipment_type' => 'Принтер старий',
            'inventory_number' => 'INV-001',
            'quantity' => 1,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [
                ['inventory_id' => $newItem->id, 'quantity' => 1],
            ],
            'replace_old_item_id' => $oldItem->id,
            'replace_action' => 'repair',
        ]);

        $response->assertRedirect(route('warehouse.index'));
        $this->assertDatabaseHas('repair_requests', [
            'old_inventory_id' => $oldItem->id,
            'branch_id' => $branch->id,
            'room_number' => '204',
            'status' => 'нова',
        ]);
        $repair = RepairRequest::where('old_inventory_id', $oldItem->id)->first();
        $this->assertStringContainsString('INV-001', $repair->description);
    }

    public function test_batch_issue_with_replacement_transfers_old_item_to_another_room(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $newItem = $this->makeWarehouseItem(5, 'Принтер HP');
        $oldItem = RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '204',
            'equipment_type' => 'Принтер старий',
            'quantity' => 1,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [
                ['inventory_id' => $newItem->id, 'quantity' => 1],
            ],
            'replace_old_item_id' => $oldItem->id,
            'replace_action' => 'transfer',
            'replace_to_branch_id' => $otherBranch->id,
            'replace_to_room_number' => '305',
        ]);

        $response->assertRedirect(route('warehouse.index'));
        $oldItem->refresh();
        $this->assertEquals($otherBranch->id, $oldItem->branch_id);
        $this->assertEquals('305', $oldItem->room_number);
        $this->assertDatabaseHas('inventory_transfers', [
            'inventory_id' => $oldItem->id,
            'to_branch_id' => $otherBranch->id,
            'to_room_number' => '305',
        ]);
    }

    public function test_batch_issue_with_replacement_returns_old_item_to_warehouse(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();
        $newItem = $this->makeWarehouseItem(5, 'Принтер HP');
        $oldItem = RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '204',
            'equipment_type' => 'Принтер старий',
            'quantity' => 1,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [
                ['inventory_id' => $newItem->id, 'quantity' => 1],
            ],
            'replace_old_item_id' => $oldItem->id,
            'replace_action' => 'warehouse',
        ]);

        $response->assertRedirect(route('warehouse.index'));
        $this->assertEquals(self::WAREHOUSE_BRANCH_ID, $oldItem->fresh()->branch_id);
    }

    public function test_room_equipment_endpoint_lists_items_in_room(): void
    {
        $admin = $this->makeAdmin();
        $branch = Branch::factory()->create();

        RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '204',
            'equipment_type' => 'Принтер',
            'quantity' => 1,
            'unit' => 'шт',
        ]);
        RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '999',
            'equipment_type' => 'Не тут',
            'quantity' => 1,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->getJson(route('warehouse.room-equipment', [
            'branch_id' => $branch->id,
            'room_number' => '204',
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['equipment_type' => 'Принтер']);
    }

    public function test_guest_cannot_batch_issue(): void
    {
        $branch = Branch::factory()->create();
        $item = $this->makeWarehouseItem(5);

        $response = $this->post(route('warehouse.issue-batch'), [
            'destination_branch_id' => $branch->id,
            'destination_room_number' => '204',
            'items' => [['inventory_id' => $item->id, 'quantity' => 1]],
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('warehouse_movements', 0);
    }
}
