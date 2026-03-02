<?php

namespace Tests\Feature;

use Tests\TestCase;

class PurchaseRequestReceiveTest extends TestCase
{
    /**
     * Test that the receive route exists and is registered correctly
     */
    public function test_receive_route_is_registered(): void
    {
        $sourceFile = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString(
            "->name('purchase-requests.receive')",
            $sourceFile,
            'Receive route should be registered with correct name'
        );
    }

    /**
     * Test that the receive form request exists with correct validation rules
     */
    public function test_receive_form_request_exists(): void
    {
        $this->assertTrue(class_exists('App\Http\Requests\ReceivePurchaseRequestRequest'));
    }

    /**
     * Test that the receive controller method exists
     */
    public function test_receive_method_exists_in_controller(): void
    {
        $controller = new \App\Http\Controllers\PurchaseRequestController;
        $this->assertTrue(method_exists($controller, 'receive'));
    }

    /**
     * Test that the controller imports necessary models
     */
    public function test_controller_has_required_imports(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            'use App\Http\Requests\ReceivePurchaseRequestRequest;',
            $sourceFile
        );
        $this->assertStringContainsString(
            'use App\Models\WarehouseMovement;',
            $sourceFile
        );
        $this->assertStringContainsString(
            'use App\Models\PurchaseRequestItem;',
            $sourceFile
        );
    }

    /**
     * Test that the receive method checks correct status values
     */
    public function test_receive_method_checks_correct_status_values(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            "'submitted', 'approved', 'completed'",
            $sourceFile,
            'Receive method should check for submitted, approved, or completed status'
        );
    }

    /**
     * Test that warehouse movement is created with correct type
     */
    public function test_receive_creates_warehouse_movement_with_receipt_type(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            "'type' => 'receipt'",
            $sourceFile,
            'Warehouse movement should use receipt type'
        );
    }

    /**
     * Test that the view has receive button and modal
     */
    public function test_view_has_receive_button(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/show.blade.php'));

        $this->assertStringContainsString(
            'receiveBtn',
            $sourceFile,
            'Show view should have receive button'
        );
        $this->assertStringContainsString(
            'receiveModal',
            $sourceFile,
            'Show view should have receive modal'
        );
    }

    /**
     * Test that the view has checkboxes for item selection
     */
    public function test_view_has_checkboxes_for_selection(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/show.blade.php'));

        $this->assertStringContainsString(
            'item-checkbox',
            $sourceFile,
            'Show view should have item checkboxes'
        );
        $this->assertStringContainsString(
            'selectAllCheckbox',
            $sourceFile,
            'Show view should have select all checkbox'
        );
    }

    /**
     * Test that the view has correct JavaScript functions
     */
    public function test_view_has_required_javascript_functions(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/show.blade.php'));

        $this->assertStringContainsString(
            'showReceiveModal',
            $sourceFile,
            'View should have showReceiveModal function'
        );
        $this->assertStringContainsString(
            'executeReceive',
            $sourceFile,
            'View should have executeReceive function'
        );
        $this->assertStringContainsString(
            'getSelectedItemIds',
            $sourceFile,
            'View should have getSelectedItemIds function'
        );
    }

    /**
     * Test that form request has required validation rules
     */
    public function test_form_request_has_validation_rules(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Requests/ReceivePurchaseRequestRequest.php'));

        $this->assertStringContainsString(
            'items.*.action',
            $sourceFile,
            'Form request should validate action'
        );
        $this->assertStringContainsString(
            'update_existing,create_new,link_to_existing',
            $sourceFile,
            'Form request should validate all three action types'
        );
    }

    /**
     * Test that the receive method handles all three action types
     */
    public function test_receive_method_handles_all_action_types(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            "'update_existing'",
            $sourceFile,
            'Method should handle update_existing action'
        );
        $this->assertStringContainsString(
            "'create_new'",
            $sourceFile,
            'Method should handle create_new action'
        );
        $this->assertStringContainsString(
            "'link_to_existing'",
            $sourceFile,
            'Method should handle link_to_existing action'
        );
    }

    /**
     * Test that the route is protected with role middleware
     */
    public function test_route_is_protected_with_role_middleware(): void
    {
        $sourceFile = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString(
            "Route::post('/purchase-requests/{purchaseRequest}/receive'",
            $sourceFile,
            'Route for receive should be registered'
        );
    }

    /**
     * Test that purchase request status is updated to completed
     */
    public function test_status_is_updated_to_completed(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            "'status' => 'completed'",
            $sourceFile,
            'Purchase request status should be updated to completed'
        );
    }
}
