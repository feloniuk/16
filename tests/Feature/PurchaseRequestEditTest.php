<?php

namespace Tests\Feature;

use App\Models\PurchaseRequestItem;
use Tests\TestCase;

class PurchaseRequestEditTest extends TestCase
{
    /**
     * Test that the edit method no longer restricts by status
     */
    public function test_edit_method_allows_any_status(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        // Assert no status restriction in edit method
        $this->assertStringNotContainsString(
            'Неможливо редагувати заявку в поточному статусі',
            $sourceFile,
            'The edit method should not restrict editing by status'
        );
    }

    /**
     * Test that the update method no longer restricts by status
     */
    public function test_update_method_allows_any_status(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        // Assert no status restriction in update method
        $this->assertStringNotContainsString(
            'Неможливо редагувати заявку в поточному статусі',
            $sourceFile,
            'The update method should not restrict editing by status'
        );
    }

    /**
     * Test that the index view shows edit button for all statuses
     */
    public function test_index_view_shows_edit_button_for_all_statuses(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/index.blade.php'));

        // Assert edit button is always rendered (no status condition wrapping it)
        $this->assertStringContainsString(
            'purchase-requests.edit',
            $sourceFile,
            'Index view should have edit button'
        );

        // Assert no status gate for edit button
        $this->assertStringNotContainsString(
            "in_array(\$request->status, ['draft', 'submitted'])",
            $sourceFile,
            'Index view should not have old status condition'
        );
    }

    /**
     * Test that the show view was updated correctly
     */
    public function test_show_view_shows_edit_button_only_for_draft(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/show.blade.php'));

        // Assert the new condition exists
        $this->assertStringContainsString(
            "\$purchaseRequest->status === 'draft'",
            $sourceFile,
            'Show view should check for draft status only'
        );

        // Assert the old condition does NOT exist
        $this->assertStringNotContainsString(
            "in_array(\$purchaseRequest->status, ['draft', 'submitted'])",
            $sourceFile,
            'Show view should not have the old condition'
        );
    }

    /**
     * Test that the edit view no longer blocks non-draft statuses
     */
    public function test_edit_view_allows_any_status(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/edit.blade.php'));

        // Assert the blocking condition is removed
        $this->assertStringNotContainsString(
            'не може бути відредагована',
            $sourceFile,
            'Edit view should not block editing for non-draft statuses'
        );

        // Assert the form is always present
        $this->assertStringContainsString(
            'purchase-requests.update',
            $sourceFile,
            'Edit view should always show the form'
        );
    }

    /**
     * Test that the factories were created
     */
    public function test_purchase_request_factory_exists(): void
    {
        $factoryPath = database_path('factories/PurchaseRequestFactory.php');
        $this->assertFileExists(
            $factoryPath,
            'PurchaseRequestFactory should be created'
        );

        $sourceFile = file_get_contents($factoryPath);
        $this->assertStringContainsString(
            'class PurchaseRequestFactory',
            $sourceFile,
            'Factory should have the correct class name'
        );
        $this->assertStringContainsString(
            'public function draft()',
            $sourceFile,
            'Factory should have draft state'
        );
        $this->assertStringContainsString(
            'public function submitted()',
            $sourceFile,
            'Factory should have submitted state'
        );
    }

    /**
     * Test that the PurchaseRequestItem factory was created
     */
    public function test_purchase_request_item_factory_exists(): void
    {
        $factoryPath = database_path('factories/PurchaseRequestItemFactory.php');
        $this->assertFileExists(
            $factoryPath,
            'PurchaseRequestItemFactory should be created'
        );

        $sourceFile = file_get_contents($factoryPath);
        $this->assertStringContainsString(
            'class PurchaseRequestItemFactory',
            $sourceFile,
            'Factory should have the correct class name'
        );
    }

    /**
     * Test that all required states exist in the factory
     */
    public function test_purchase_request_factory_has_all_states(): void
    {
        $sourceFile = file_get_contents(database_path('factories/PurchaseRequestFactory.php'));

        $states = ['draft', 'submitted', 'approved', 'rejected', 'completed'];
        foreach ($states as $state) {
            $this->assertStringContainsString(
                "public function {$state}()",
                $sourceFile,
                "Factory should have {$state} state"
            );
        }
    }

    /**
     * Test that approve method exists and checks authorization
     */
    public function test_approve_method_checks_authorization(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        // Assert approve method exists
        $this->assertStringContainsString(
            'public function approve(PurchaseRequest $purchaseRequest)',
            $sourceFile,
            'Approve method should exist'
        );

        // Assert it checks for submitted status
        $this->assertStringContainsString(
            "\$purchaseRequest->status !== 'submitted'",
            $sourceFile,
            'Approve method should check for submitted status'
        );

        // Assert it checks for admin or director role
        $this->assertStringContainsString(
            "in_array(Auth::user()->role, ['admin', 'director'])",
            $sourceFile,
            'Approve method should check for admin or director role'
        );
    }

    /**
     * Test that reject method exists and checks authorization
     */
    public function test_reject_method_checks_authorization(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        // Assert reject method exists
        $this->assertStringContainsString(
            'public function reject(PurchaseRequest $purchaseRequest)',
            $sourceFile,
            'Reject method should exist'
        );

        // Assert it checks for submitted status
        $this->assertStringContainsString(
            "\$purchaseRequest->status !== 'submitted'",
            $sourceFile,
            'Reject method should check for submitted status'
        );

        // Assert it checks for admin or director role
        $this->assertStringContainsString(
            "in_array(Auth::user()->role, ['admin', 'director'])",
            $sourceFile,
            'Reject method should check for admin or director role'
        );
    }

    /**
     * Test that routes were added for approve and reject
     */
    public function test_approve_and_reject_routes_exist(): void
    {
        $sourceFile = file_get_contents(base_path('routes/web.php'));

        // Assert approve route exists
        $this->assertStringContainsString(
            'purchase-requests.approve',
            $sourceFile,
            'Approve route should be registered'
        );

        // Assert reject route exists
        $this->assertStringContainsString(
            'purchase-requests.reject',
            $sourceFile,
            'Reject route should be registered'
        );

        // Assert routes are protected by admin,director middleware
        $this->assertStringContainsString(
            "Route::middleware('role:admin,director')",
            $sourceFile,
            'Routes should be protected by admin,director middleware'
        );
    }

    /**
     * Test that show view has approve and reject buttons
     */
    public function test_show_view_has_approve_reject_buttons(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/show.blade.php'));

        // Assert approve button exists
        $this->assertStringContainsString(
            'purchase-requests.approve',
            $sourceFile,
            'Show view should have approve button'
        );

        // Assert reject button exists
        $this->assertStringContainsString(
            'purchase-requests.reject',
            $sourceFile,
            'Show view should have reject button'
        );

        // Assert buttons are shown only for submitted status and admin/director
        $this->assertStringContainsString(
            "status === 'submitted'",
            $sourceFile,
            'Buttons should be shown only for submitted status'
        );

        $this->assertStringContainsString(
            "in_array(Auth::user()->role, ['admin', 'director'])",
            $sourceFile,
            'Buttons should be shown only for admin/director users'
        );
    }
}
