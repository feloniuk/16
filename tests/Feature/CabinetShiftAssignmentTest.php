<?php

namespace Tests\Feature;

use App\Models\Cabinet;
use App\Models\CabinetShift;
use App\Models\MedicalStaff;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabinetShiftAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_cabinet_shift_number_is_unique_per_cabinet(): void
    {
        $cabinet = Cabinet::factory()->create();
        CabinetShift::factory()->for($cabinet)->create(['shift' => CabinetShift::SHIFT_FIRST]);

        $this->expectException(QueryException::class);

        CabinetShift::factory()->for($cabinet)->create(['shift' => CabinetShift::SHIFT_FIRST]);
    }

    public function test_assigning_a_nurse_as_doctor_is_rejected(): void
    {
        $admin = $this->admin();
        $shift = CabinetShift::factory()->create();
        $nurse = MedicalStaff::factory()->nurse()->create();

        $response = $this->actingAs($admin)->patch(route('cabinet-shifts.update', $shift), [
            'doctor_id' => $nurse->id,
        ]);

        $response->assertSessionHasErrors('doctor_id');
        $this->assertNull($shift->fresh()->doctor_id);
    }

    public function test_assigning_a_doctor_as_nurse_is_rejected(): void
    {
        $admin = $this->admin();
        $shift = CabinetShift::factory()->create();
        $doctor = MedicalStaff::factory()->doctor()->create();

        $response = $this->actingAs($admin)->patch(route('cabinet-shifts.update', $shift), [
            'nurse_id' => $doctor->id,
        ]);

        $response->assertSessionHasErrors('nurse_id');
        $this->assertNull($shift->fresh()->nurse_id);
    }

    public function test_admin_can_assign_and_unassign_a_shift(): void
    {
        $admin = $this->admin();
        $shift = CabinetShift::factory()->create();
        $doctor = MedicalStaff::factory()->doctor()->create();
        $nurse = MedicalStaff::factory()->nurse()->create();

        $response = $this->actingAs($admin)->patch(route('cabinet-shifts.update', $shift), [
            'doctor_id' => $doctor->id,
            'nurse_id' => $nurse->id,
        ]);

        $response->assertRedirect();
        $shift->refresh();
        $this->assertSame($doctor->id, $shift->doctor_id);
        $this->assertSame($nurse->id, $shift->nurse_id);

        $this->actingAs($admin)->patch(route('cabinet-shifts.update', $shift), [
            'doctor_id' => null,
            'nurse_id' => null,
        ]);

        $shift->refresh();
        $this->assertNull($shift->doctor_id);
        $this->assertNull($shift->nurse_id);
    }
}
