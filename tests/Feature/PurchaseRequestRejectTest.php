<?php

namespace Tests\Feature;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestRejectTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_reject_returns_request_to_draft_instead_of_terminal_rejected(): void
    {
        $admin = $this->makeAdmin();
        $purchaseRequest = PurchaseRequest::factory()->for($admin)->submitted()->create();

        $response = $this->actingAs($admin)->post(route('purchase-requests.reject', $purchaseRequest));

        $response->assertRedirect(route('purchase-requests.show', $purchaseRequest));
        $this->assertSame('draft', $purchaseRequest->fresh()->status);
    }

    public function test_rejected_request_can_be_resubmitted(): void
    {
        $admin = $this->makeAdmin();
        $purchaseRequest = PurchaseRequest::factory()->for($admin)->submitted()->create();

        $this->actingAs($admin)->post(route('purchase-requests.reject', $purchaseRequest));

        $response = $this->actingAs($admin)->post(route('purchase-requests.submit', $purchaseRequest));

        $response->assertRedirect();
        $this->assertSame('submitted', $purchaseRequest->fresh()->status);
    }
}
