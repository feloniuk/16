<?php

namespace Tests\Feature;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestPrintPeriodTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_update_saves_print_month_and_year(): void
    {
        $admin = $this->makeAdmin();
        $purchaseRequest = PurchaseRequest::factory()->for($admin)->create([
            'requested_date' => now()->addDays(5),
        ]);
        $purchaseRequest->items()->create([
            'item_name' => 'Папір А4',
            'quantity' => 5,
            'unit' => 'шт',
        ]);

        $response = $this->actingAs($admin)->patch(route('purchase-requests.update', $purchaseRequest), [
            'description' => $purchaseRequest->description,
            'requested_date' => $purchaseRequest->requested_date->format('Y-m-d'),
            'print_month' => 'вересня',
            'print_year' => 2027,
            'notes' => null,
            'items' => [
                ['id' => $purchaseRequest->items->first()->id, 'item_name' => 'Папір А4', 'quantity' => 5, 'unit' => 'шт'],
            ],
        ]);

        $response->assertRedirect(route('purchase-requests.show', $purchaseRequest));
        $this->assertSame('вересня', $purchaseRequest->fresh()->print_month);
        $this->assertSame(2027, $purchaseRequest->fresh()->print_year);
    }

    public function test_print_view_uses_saved_month_and_year(): void
    {
        $admin = $this->makeAdmin();
        $purchaseRequest = PurchaseRequest::factory()->for($admin)->create([
            'print_month' => 'лютого',
            'print_year' => 2025,
        ]);

        $response = $this->actingAs($admin)->get(route('purchase-requests.print', $purchaseRequest));

        $response->assertStatus(200);
        $response->assertSee('ЗАЯВКА на лютого 2025 року', false);
    }

    public function test_print_view_falls_back_to_current_year_when_unset(): void
    {
        $admin = $this->makeAdmin();
        $purchaseRequest = PurchaseRequest::factory()->for($admin)->create();

        // Simulate a pre-existing request created before print_month/print_year existed.
        $purchaseRequest->forceFill(['print_month' => null, 'print_year' => null])->saveQuietly();

        $response = $this->actingAs($admin)->get(route('purchase-requests.print', $purchaseRequest));

        $response->assertStatus(200);
        $response->assertSee('ЗАЯВКА на ______ '.date('Y').' року', false);
    }
}
