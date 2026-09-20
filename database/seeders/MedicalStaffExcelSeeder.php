<?php

// database/seeders/MedicalStaffExcelSeeder.php

namespace Database\Seeders;

use App\Models\Ambulatoriya;
use App\Models\MedicalStaff;
use App\Services\MedicalStaffImportParser;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MedicalStaffExcelSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('Список працівників станом на 18.09.2026р..xlsx');

        if (! file_exists($path)) {
            $this->command?->warn("Файл не знайдено: {$path}");

            return;
        }

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $parser = new MedicalStaffImportParser;

        $imported = ['doctor' => 0, 'nurse' => 0];
        $skippedVacant = 0;
        $skippedUnresolvedAmbulatoriya = 0;

        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber === 1) {
                continue;
            }

            $parsed = $parser->parseRow(
                lastName: $row['A'] ?? null,
                firstName: $row['B'] ?? null,
                middleName: $row['C'] ?? null,
                employmentStatus: $row['D'] ?? null,
                position: $row['E'] ?? null,
                positionStatus: $row['F'] ?? null,
                department: $row['G'] ?? null,
            );

            if ($parsed === null) {
                if (trim((string) ($row['F'] ?? '')) === 'вільна тимчасово (декрет, навчання, тощо)') {
                    $skippedVacant++;
                }

                continue;
            }

            $ambulatoriyaId = null;

            if ($parsed['ambulatoriya_name'] !== null) {
                $ambulatoriya = Ambulatoriya::firstOrCreate([
                    'name' => $parsed['ambulatoriya_name'],
                ]);
                $ambulatoriyaId = $ambulatoriya->id;
            } else {
                $skippedUnresolvedAmbulatoriya++;
                $this->command?->warn("Рядок {$rowNumber}: не вдалося визначити амбулаторію для {$parsed['last_name']} {$parsed['first_name']}");
            }

            MedicalStaff::create([
                'type' => $parsed['type'],
                'last_name' => $parsed['last_name'],
                'first_name' => $parsed['first_name'],
                'middle_name' => $parsed['middle_name'],
                'position' => $parsed['position'],
                'employment_status' => $parsed['employment_status'],
                'position_status' => $parsed['position_status'],
                'ambulatoriya_id' => $ambulatoriyaId,
                'is_active' => true,
            ]);

            $imported[$parsed['type']]++;
        }

        $this->command?->info("Імпортовано лікарів: {$imported['doctor']}");
        $this->command?->info("Імпортовано медсестер: {$imported['nurse']}");
        $this->command?->info("Пропущено вакантних посад: {$skippedVacant}");

        if ($skippedUnresolvedAmbulatoriya > 0) {
            $this->command?->warn("Рядків без визначеної амбулаторії: {$skippedUnresolvedAmbulatoriya}");
        }

        $unassignedCount = Ambulatoriya::whereNull('branch_id')->count();

        if ($unassignedCount > 0) {
            $this->command?->warn("Увага: {$unassignedCount} амбулаторій без прив'язки до філії — призначте філію вручну через адмінку.");
        }
    }
}
