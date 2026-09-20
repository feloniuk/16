<?php

namespace Tests\Feature;

use App\Models\Ambulatoriya;
use App\Models\MedicalStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalStaffControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_admin_can_view_medical_staff_index(): void
    {
        $admin = $this->admin();
        $ambulatoriya = Ambulatoriya::factory()->create();
        MedicalStaff::factory()->doctor()->create(['ambulatoriya_id' => $ambulatoriya->id]);
        MedicalStaff::factory()->nurse()->create(['ambulatoriya_id' => $ambulatoriya->id]);

        $response = $this->actingAs($admin)->get(route('medical-staff.index'));

        $response->assertOk();
    }

    public function test_index_can_be_filtered_by_type(): void
    {
        $admin = $this->admin();
        MedicalStaff::factory()->doctor()->create(['last_name' => 'ДокторПрізвище']);
        MedicalStaff::factory()->nurse()->create(['last_name' => 'МедсестраПрізвище']);

        $response = $this->actingAs($admin)->get(route('medical-staff.index', ['type' => 'doctor']));

        $response->assertOk();
        $response->assertSee('ДокторПрізвище');
        $response->assertDontSee('МедсестраПрізвище');
    }

    public function test_warehouse_keeper_cannot_access_medical_staff(): void
    {
        $keeper = User::factory()->create(['role' => 'warehouse_keeper', 'is_active' => true]);

        $response = $this->actingAs($keeper)->get(route('medical-staff.index'));

        $response->assertForbidden();
    }
}
