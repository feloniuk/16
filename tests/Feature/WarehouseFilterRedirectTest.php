<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseFilterRedirectTest extends TestCase
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

    private function makeWarehouseItem(string $equipmentType, int $quantity = 5): RoomInventory
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

    public function test_issue_by_name_redirects_back_to_previous_filtered_page(): void
    {
        $admin = $this->makeAdmin();
        $this->makeWarehouseItem('Папір А4', 10);

        $previousUrl = route('warehouse.index', ['search' => 'папір', 'page' => 2]);

        $response = $this->actingAs($admin)
            ->from($previousUrl)
            ->post(route('warehouse.issue-by-name'), [
                'equipment_type' => 'Папір А4',
                'quantity' => 2,
            ]);

        $response->assertRedirect($previousUrl);
        $response->assertSessionHas('success');
    }

    public function test_receipt_by_name_redirects_back_to_previous_filtered_page(): void
    {
        $admin = $this->makeAdmin();
        $this->makeWarehouseItem('Ручка кулькова', 3);

        $previousUrl = route('warehouse.index', ['low_stock' => 1, 'page' => 3]);

        $response = $this->actingAs($admin)
            ->from($previousUrl)
            ->post(route('warehouse.receipt-by-name'), [
                'equipment_type' => 'Ручка кулькова',
                'quantity' => 5,
            ]);

        $response->assertRedirect($previousUrl);
        $response->assertSessionHas('success');
    }

    public function test_issue_batch_redirects_back_to_previous_filtered_page(): void
    {
        $admin = $this->makeAdmin();
        $item = $this->makeWarehouseItem('Степлер', 8);
        $destinationBranch = Branch::factory()->create();

        $previousUrl = route('warehouse.index', ['category' => 'all', 'page' => 2]);

        $response = $this->actingAs($admin)
            ->from($previousUrl)
            ->post(route('warehouse.issue-batch'), [
                'destination_branch_id' => $destinationBranch->id,
                'destination_room_number' => '101',
                'items' => [
                    ['inventory_id' => $item->id, 'quantity' => 1],
                ],
            ]);

        $response->assertRedirect($previousUrl);
        $response->assertSessionHas('success');
    }
}
