<?php

namespace Tests\Feature;

use App\Models\Ambulatoriya;
use App\Models\MedicalStaff;
use Database\Seeders\MedicalStaffExcelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalStaffExcelSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_imports_doctors_and_nurses_from_the_real_file(): void
    {
        $path = base_path('Список працівників станом на 18.09.2026р..xlsx');

        if (! file_exists($path)) {
            $this->markTestSkipped('Джерело даних не знайдено: '.$path);
        }

        (new MedicalStaffExcelSeeder)->run();

        $this->assertSame(57, MedicalStaff::doctors()->count());
        $this->assertSame(68, MedicalStaff::nurses()->count());
        $this->assertSame(10, Ambulatoriya::count());
        $this->assertSame(10, Ambulatoriya::whereNull('branch_id')->count());
    }
}
