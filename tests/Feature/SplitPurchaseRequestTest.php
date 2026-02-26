<?php

namespace Tests\Feature;

use Tests\TestCase;

class SplitPurchaseRequestTest extends TestCase
{
    /**
     * Test that split route exists in web.php
     */
    public function test_split_route_exists(): void
    {
        $sourceFile = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString(
            'purchase-requests.split',
            $sourceFile,
            'Split route should be registered'
        );

        $this->assertStringContainsString(
            'split',
            $sourceFile,
            'Split route should reference split method'
        );
    }

    /**
     * Test that split method exists in controller
     */
    public function test_split_method_exists_in_controller(): void
    {
        $sourceFile = file_get_contents(app_path('Http/Controllers/PurchaseRequestController.php'));

        $this->assertStringContainsString(
            'public function split(Request $request, PurchaseRequest $purchaseRequest)',
            $sourceFile,
            'Split method should exist in controller'
        );
    }

    /**
     * Test split functionality in edit view has checkboxes
     */
    public function test_edit_view_has_checkboxes_for_items(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/edit.blade.php'));

        $this->assertStringContainsString(
            'item-checkbox',
            $sourceFile,
            'Edit view should have checkboxes for items'
        );

        $this->assertStringContainsString(
            'selectAllCheckbox',
            $sourceFile,
            'Edit view should have select all checkbox'
        );
    }

    /**
     * Test split functionality button exists in edit view
     */
    public function test_edit_view_has_split_button(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/edit.blade.php'));

        $this->assertStringContainsString(
            'splitBtn',
            $sourceFile,
            'Edit view should have split button'
        );

        $this->assertStringContainsString(
            'showSplitModal',
            $sourceFile,
            'Edit view should have showSplitModal function'
        );
    }

    /**
     * Test split modal exists in edit view
     */
    public function test_edit_view_has_split_modal(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/edit.blade.php'));

        $this->assertStringContainsString(
            'splitModal',
            $sourceFile,
            'Edit view should have split modal'
        );

        $this->assertStringContainsString(
            'newDescription',
            $sourceFile,
            'Edit view should have description input for new request'
        );

        $this->assertStringContainsString(
            'newRequestedDate',
            $sourceFile,
            'Edit view should have date input for new request'
        );
    }

    /**
     * Test split JavaScript functions exist in edit view
     */
    public function test_edit_view_has_split_javascript_functions(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/edit.blade.php'));

        $this->assertStringContainsString(
            'function getSelectedItemIndices()',
            $sourceFile,
            'Should have getSelectedItemIndices function'
        );

        $this->assertStringContainsString(
            'function updateSplitButtonVisibility()',
            $sourceFile,
            'Should have updateSplitButtonVisibility function'
        );

        $this->assertStringContainsString(
            'function executeSplit()',
            $sourceFile,
            'Should have executeSplit function'
        );
    }

    /**
     * Test create view has duplicate add item button
     */
    public function test_create_view_has_duplicate_add_button(): void
    {
        $sourceFile = file_get_contents(resource_path('views/purchase-requests/create.blade.php'));

        // Count occurrences of "onclick=\"addItemRow()\""
        $matches = substr_count($sourceFile, 'onclick="addItemRow()');

        $this->assertGreaterThanOrEqual(
            2,
            $matches,
            'Create view should have at least 2 add item buttons (one in header, one below table)'
        );

        // Verify there's a button below the table
        $this->assertStringContainsString(
            'mt-3',
            $sourceFile,
            'Should have spacing class for button below table'
        );
    }
}
