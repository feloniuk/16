<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Cabinet;
use App\Models\CabinetShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabinetControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_admin_can_view_cabinets_index(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        Cabinet::factory()->for($branch)->create();

        $response = $this->actingAs($admin)->get(route('cabinets.index'));

        $response->assertOk();
    }

    public function test_warehouse_keeper_cannot_access_cabinets(): void
    {
        $keeper = User::factory()->create(['role' => 'warehouse_keeper', 'is_active' => true]);

        $response = $this->actingAs($keeper)->get(route('cabinets.index'));

        $response->assertForbidden();
    }

    public function test_creating_a_cabinet_also_creates_two_shifts(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();

        $response = $this->actingAs($admin)->post(route('cabinets.store'), [
            'branch_id' => $branch->id,
            'number' => '101',
        ]);

        $response->assertRedirect();

        $cabinet = Cabinet::where('branch_id', $branch->id)->where('number', '101')->firstOrFail();
        $this->assertCount(2, $cabinet->shifts);
        $this->assertEqualsCanonicalizing(
            [CabinetShift::SHIFT_FIRST, CabinetShift::SHIFT_SECOND],
            $cabinet->shifts->pluck('shift')->all()
        );
    }

    public function test_cannot_delete_cabinet_with_assigned_shift(): void
    {
        $admin = $this->admin();
        $cabinet = Cabinet::factory()->create();
        $shift = CabinetShift::factory()->for($cabinet)->create();
        $shift->update(['doctor_id' => \App\Models\MedicalStaff::factory()->doctor()->create()->id]);

        $response = $this->actingAs($admin)->delete(route('cabinets.destroy', $cabinet));

        $response->assertSessionHasErrors();
        $this->assertNotNull($cabinet->fresh());
    }

    public function test_admin_can_view_cabinet_edit_page_with_shifts(): void
    {
        $admin = $this->admin();
        $cabinet = Cabinet::factory()->create();
        CabinetShift::factory()->for($cabinet)->create(['shift' => CabinetShift::SHIFT_FIRST]);
        CabinetShift::factory()->for($cabinet)->create(['shift' => CabinetShift::SHIFT_SECOND]);

        $response = $this->actingAs($admin)->get(route('cabinets.edit', $cabinet));

        $response->assertOk();
        $response->assertSee('Зміна 1');
        $response->assertSee('Зміна 2');
    }

    public function test_cabinet_number_must_be_unique_per_branch(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        Cabinet::factory()->for($branch)->create(['number' => '101']);

        $response = $this->actingAs($admin)->post(route('cabinets.store'), [
            'branch_id' => $branch->id,
            'number' => '101',
        ]);

        $response->assertSessionHasErrors('number');
    }
}
