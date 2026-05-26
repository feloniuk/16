<?php

namespace Tests\Feature;

use App\Http\Controllers\RepairOrderController;
use App\Http\Requests\ReceiveRepairOrderItemsRequest;
use App\Models\RepairOrder;
use App\Models\RepairOrderItem;
use Tests\TestCase;

class RepairOrderReceiveTest extends TestCase
{
    public function test_receive_items_route_is_registered(): void
    {
        $routes = collect(\Route::getRoutes())->map(fn ($r) => $r->getName())->filter()->values();
        $this->assertContains('repair-orders.receive-items', $routes->toArray());
    }

    public function test_receive_items_request_exists(): void
    {
        $this->assertTrue(class_exists(ReceiveRepairOrderItemsRequest::class));
    }

    public function test_receive_items_method_exists_in_controller(): void
    {
        $this->assertTrue(method_exists(RepairOrderController::class, 'receiveItems'));
    }

    public function test_receive_items_request_has_validation_rules(): void
    {
        $request = new ReceiveRepairOrderItemsRequest;
        $rules = $request->rules();

        $this->assertArrayHasKey('item_ids', $rules);
        $this->assertArrayHasKey('item_ids.*', $rules);
        $this->assertArrayHasKey('returned_at', $rules);
        $this->assertArrayHasKey('return_document_number', $rules);
    }

    public function test_repair_order_item_has_return_fields(): void
    {
        $item = new RepairOrderItem;
        $this->assertContains('returned_at', $item->getFillable());
        $this->assertContains('return_document_number', $item->getFillable());
    }

    public function test_repair_order_item_is_returned_method(): void
    {
        $item = new RepairOrderItem(['returned_at' => null]);
        $this->assertFalse($item->isReturned());

        $item->returned_at = now()->toDateString();
        $this->assertTrue($item->isReturned());
    }

    public function test_repair_order_can_receive_items_for_valid_statuses(): void
    {
        foreach (['sent', 'in_repair', 'approved'] as $status) {
            $order = new RepairOrder(['status' => $status]);
            $this->assertTrue($order->canReceiveItems(), "Status '{$status}' should allow receiving");
        }
    }

    public function test_repair_order_cannot_receive_items_for_invalid_statuses(): void
    {
        foreach (['draft', 'pending_approval', 'completed', 'cancelled', 'rejected'] as $status) {
            $order = new RepairOrder(['status' => $status]);
            $this->assertFalse($order->canReceiveItems(), "Status '{$status}' should not allow receiving");
        }
    }

    public function test_all_items_returned_method(): void
    {
        $order = new RepairOrder;
        $order->setRelation('items', collect([
            new RepairOrderItem(['returned_at' => now()->toDateString()]),
            new RepairOrderItem(['returned_at' => now()->toDateString()]),
        ]));
        $this->assertTrue($order->allItemsReturned());

        $order->setRelation('items', collect([
            new RepairOrderItem(['returned_at' => now()->toDateString()]),
            new RepairOrderItem(['returned_at' => null]),
        ]));
        $this->assertFalse($order->allItemsReturned());
    }

    public function test_return_fields_migration_exists(): void
    {
        $migrationFiles = glob(database_path('migrations/*add_return_fields_to_repair_order_items*'));
        $this->assertNotEmpty($migrationFiles, 'Migration for return fields should exist');
    }

    public function test_show_view_has_receive_button(): void
    {
        $view = file_get_contents(resource_path('views/repair-orders/show.blade.php'));
        $this->assertStringContainsString('receiveModal', $view);
        $this->assertStringContainsString('repair-orders.receive-items', $view);
    }

    public function test_show_view_displays_serial_number(): void
    {
        $view = file_get_contents(resource_path('views/repair-orders/show.blade.php'));
        $this->assertStringContainsString('serial_number', $view);
    }

    public function test_show_view_displays_return_status_per_item(): void
    {
        $view = file_get_contents(resource_path('views/repair-orders/show.blade.php'));
        $this->assertStringContainsString('returned_at', $view);
        $this->assertStringContainsString('return_document_number', $view);
        $this->assertStringContainsString('isReturned', $view);
    }

    public function test_index_view_displays_sent_date(): void
    {
        $view = file_get_contents(resource_path('views/repair-orders/index.blade.php'));
        $this->assertStringContainsString('sent_date', $view);
    }

    public function test_index_view_displays_return_progress(): void
    {
        $view = file_get_contents(resource_path('views/repair-orders/index.blade.php'));
        $this->assertStringContainsString('returnedCount', $view);
    }
}
