<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\User;
use App\Models\WarehouseMovement;
use App\Models\WriteoffRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriteoffRequestMovementsTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function makeInventoryItemWithMovement(string $date): RoomInventory
    {
        $branch = Branch::factory()->create();

        $item = RoomInventory::create([
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => '1',
            'equipment_type' => 'Папір',
            'quantity' => 10,
            'unit' => 'шт',
        ]);

        WarehouseMovement::create([
            'user_id' => User::factory()->create()->id,
            'inventory_id' => $item->id,
            'type' => 'issue',
            'quantity' => -5,
            'balance_after' => 5,
            'operation_date' => $date,
        ]);

        return $item;
    }

    public function test_movements_endpoint_returns_json_for_date_range(): void
    {
        $admin = $this->makeAdmin();
        $this->makeInventoryItemWithMovement(now()->toDateString());
        $writeoffRequest = WriteoffRequest::create([
            'user_id' => $admin->id,
            'status' => 'draft',
            'writeoff_date' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('writeoff-requests.movements', $writeoffRequest).'?date_from='.now()->startOfMonth()->toDateString().'&date_to='.now()->toDateString());

        $response->assertStatus(200);
        $response->assertJsonStructure(['movements' => [['inventory_id', 'item_name', 'unit', 'total_quantity']]]);
        $response->assertJsonCount(1, 'movements');
    }

    public function test_movements_endpoint_respects_date_filter(): void
    {
        $admin = $this->makeAdmin();
        $this->makeInventoryItemWithMovement('2024-01-15');
        $writeoffRequest = WriteoffRequest::create([
            'user_id' => $admin->id,
            'status' => 'draft',
            'writeoff_date' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('writeoff-requests.movements', $writeoffRequest).'?date_from=2025-01-01&date_to=2025-01-31');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'movements');
    }

    public function test_edit_view_does_not_reload_via_get_filter_form(): void
    {
        $sourceFile = file_get_contents(resource_path('views/writeoff-requests/edit.blade.php'));

        $this->assertStringNotContainsString(
            "method=\"GET\" action=\"{{ route('writeoff-requests.edit'",
            $sourceFile,
            'The movements date filter should not reload the whole edit page via GET, since that discards unsaved item changes'
        );
    }
}
