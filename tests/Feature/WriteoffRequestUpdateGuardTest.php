<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WriteoffRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriteoffRequestUpdateGuardTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function makeWriteoffRequest(string $status): WriteoffRequest
    {
        $admin = $this->makeAdmin();

        $writeoffRequest = WriteoffRequest::create([
            'user_id' => $admin->id,
            'status' => $status,
            'writeoff_date' => now(),
        ]);

        $writeoffRequest->items()->create([
            'item_name' => 'Папір',
            'unit' => 'шт',
            'quantity' => 1,
        ]);

        return $writeoffRequest;
    }

    public function test_update_is_blocked_for_approved_writeoff_request(): void
    {
        $writeoffRequest = $this->makeWriteoffRequest('approved');
        $item = $writeoffRequest->items()->first();
        $admin = User::find($writeoffRequest->user_id);

        $response = $this->actingAs($admin)->put(route('writeoff-requests.update', $writeoffRequest), [
            'writeoff_date' => now()->toDateString(),
            'items' => [
                ['id' => $item->id, 'item_name' => 'Папір', 'unit' => 'шт', 'quantity' => 999],
            ],
        ]);

        $response->assertRedirect(route('writeoff-requests.show', $writeoffRequest));
        $response->assertSessionHasErrors();
        $this->assertSame(1, $writeoffRequest->items()->first()->quantity);
    }

    public function test_update_is_blocked_for_completed_writeoff_request(): void
    {
        $writeoffRequest = $this->makeWriteoffRequest('completed');
        $item = $writeoffRequest->items()->first();
        $admin = User::find($writeoffRequest->user_id);

        $response = $this->actingAs($admin)->put(route('writeoff-requests.update', $writeoffRequest), [
            'writeoff_date' => now()->toDateString(),
            'items' => [
                ['id' => $item->id, 'item_name' => 'Папір', 'unit' => 'шт', 'quantity' => 999],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertSame(1, $writeoffRequest->items()->first()->quantity);
    }

    public function test_update_is_allowed_for_draft_writeoff_request(): void
    {
        $writeoffRequest = $this->makeWriteoffRequest('draft');
        $item = $writeoffRequest->items()->first();
        $admin = User::find($writeoffRequest->user_id);

        $response = $this->actingAs($admin)->put(route('writeoff-requests.update', $writeoffRequest), [
            'writeoff_date' => now()->toDateString(),
            'items' => [
                ['id' => $item->id, 'inventory_id' => null, 'item_name' => 'Папір', 'unit' => 'шт', 'quantity' => 10],
            ],
        ]);

        $response->assertRedirect(route('writeoff-requests.show', $writeoffRequest));
        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(10, $writeoffRequest->items()->first()->quantity);
    }

    public function test_update_is_allowed_for_submitted_writeoff_request(): void
    {
        $writeoffRequest = $this->makeWriteoffRequest('submitted');
        $item = $writeoffRequest->items()->first();
        $admin = User::find($writeoffRequest->user_id);

        $response = $this->actingAs($admin)->put(route('writeoff-requests.update', $writeoffRequest), [
            'writeoff_date' => now()->toDateString(),
            'items' => [
                ['id' => $item->id, 'inventory_id' => null, 'item_name' => 'Папір', 'unit' => 'шт', 'quantity' => 7],
            ],
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(7, $writeoffRequest->items()->first()->quantity);
    }
}
