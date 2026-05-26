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

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function makeWarehouseKeeper(): User
    {
        return User::factory()->create(['role' => 'warehouse_keeper', 'is_active' => true]);
    }

    private function makeInventoryItem(int $quantity = 10): RoomInventory
    {
        $branch = Branch::factory()->create();

        return RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '1',
            'equipment_type' => 'Картридж',
            'full_name' => 'Картридж HP 85A',
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
            'inventory_id' => $item->id,
            'quantity' => 2,
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
            'inventory_id' => $item->id,
            'quantity' => 3,
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
            'inventory_id' => $item->id,
            'quantity' => 4,
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertEquals(6, $item->fresh()->quantity);
    }

    public function test_repair_issue_note_contains_repair_reference(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(5);
        $repair = $this->makeRepair();

        $this->actingAs($admin)->post(route('repairs.issue-from-warehouse', $repair), [
            'inventory_id' => $item->id,
            'quantity' => 1,
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
            'inventory_id' => $item->id,
            'quantity' => 5,
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertEquals(2, $item->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_warehouse_keeper_can_issue_for_repair(): void
    {
        $keeper = $this->makeWarehouseKeeper();
        $item = $this->makeInventoryItem(5);
        $repair = $this->makeRepair();

        $response = $this->actingAs($keeper)->post(route('repairs.issue-from-warehouse', $repair), [
            'inventory_id' => $item->id,
            'quantity' => 1,
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
            'inventory_id' => $item->id,
            'quantity' => 1,
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
            'inventory_id' => $item->id,
            'quantity' => 1,
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
            'inventory_id' => $item->id,
            'quantity' => 2,
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
            'inventory_id' => $item->id,
            'quantity' => 3,
            'operation_date' => today()->toDateString(),
        ]);

        $this->assertEquals(7, $item->fresh()->quantity);
    }

    public function test_cartridge_issue_note_contains_cartridge_reference(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeInventoryItem(5);
        $cartridge = $this->makeCartridge();

        $this->actingAs($admin)->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'inventory_id' => $item->id,
            'quantity' => 1,
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
            'inventory_id' => $item->id,
            'quantity' => 5,
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertEquals(1, $item->fresh()->quantity);
        $this->assertDatabaseCount('warehouse_movements', 0);
    }

    public function test_guest_cannot_issue_for_cartridge(): void
    {
        $item = $this->makeInventoryItem(5);
        $cartridge = $this->makeCartridge();

        $response = $this->post(route('cartridges.issue-from-warehouse', $cartridge), [
            'inventory_id' => $item->id,
            'quantity' => 1,
            'operation_date' => today()->toDateString(),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('warehouse_movements', 0);
    }
}
