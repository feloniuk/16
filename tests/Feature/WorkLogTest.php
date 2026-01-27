<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_work_logs_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('work-logs.index'));

        $response->assertOk();
        $response->assertViewIs('work-logs.index');
    }

    public function test_director_can_view_work_logs_index(): void
    {
        $director = User::factory()->create(['role' => 'director', 'is_active' => true]);

        $response = $this->actingAs($director)->get(route('work-logs.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_work_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $branch = Branch::factory()->create();

        $data = [
            'work_type' => 'manual',
            'description' => 'Test work log entry',
            'branch_id' => $branch->id,
            'room_number' => '101',
            'performed_at' => today()->toDateString(),
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($admin)->post(route('work-logs.store'), $data);

        $response->assertRedirect(route('work-logs.index'));
        $this->assertDatabaseHas('work_logs', [
            'work_type' => 'manual',
            'description' => 'Test work log entry',
            'user_id' => $admin->id,
        ]);
    }

    public function test_director_cannot_create_work_log(): void
    {
        $director = User::factory()->create(['role' => 'director', 'is_active' => true]);
        $branch = Branch::factory()->create();

        $data = [
            'work_type' => 'manual',
            'description' => 'Test work log entry',
            'branch_id' => $branch->id,
            'room_number' => '101',
            'performed_at' => today()->toDateString(),
        ];

        $response = $this->actingAs($director)->post(route('work-logs.store'), $data);

        $response->assertForbidden();
    }

    public function test_work_log_requires_description(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $branch = Branch::factory()->create();

        $data = [
            'work_type' => 'manual',
            'description' => '',
            'branch_id' => $branch->id,
            'room_number' => '101',
            'performed_at' => today()->toDateString(),
        ];

        $response = $this->actingAs($admin)->post(route('work-logs.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_can_view_work_log_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $workLog = WorkLog::factory()->create();

        $response = $this->actingAs($admin)->get(route('work-logs.show', $workLog));

        $response->assertOk();
        $response->assertSee($workLog->description);
    }

    public function test_admin_can_delete_work_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $workLog = WorkLog::factory()->create();

        $response = $this->actingAs($admin)->delete(route('work-logs.destroy', $workLog));

        $response->assertRedirect(route('work-logs.index'));
        $this->assertDatabaseMissing('work_logs', ['id' => $workLog->id]);
    }
}
