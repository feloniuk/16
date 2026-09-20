<?php

namespace App\Services;

use App\Models\MedicalStaff;

class MedicalStaffImportParser
{
    /**
     * @var array<string, string>
     */
    private const RELEVANT_POSITIONS = [
        'лікар загальної практики - сімейний лікар' => MedicalStaff::TYPE_DOCTOR,
        'сестра медична загальної практики - сімейна медицина' => MedicalStaff::TYPE_NURSE,
    ];

    private const VACANT_POSITION_STATUS = 'вільна тимчасово';

    /**
     * Parse a single spreadsheet row into a staff attribute array, or null if the row
     * should be skipped (irrelevant position, or a vacant position with nobody in it).
     *
     * @return array{type: string, last_name: string, first_name: string, middle_name: ?string, position: string, employment_status: ?string, position_status: ?string, ambulatoriya_name: ?string}|null
     */
    public function parseRow(
        ?string $lastName,
        ?string $firstName,
        ?string $middleName,
        ?string $employmentStatus,
        ?string $position,
        ?string $positionStatus,
        ?string $department,
    ): ?array {
        $lastName = trim((string) $lastName);
        $firstName = trim((string) $firstName);
        $position = trim((string) $position);

        if ($lastName === '' || $firstName === '') {
            return null;
        }

        $type = self::RELEVANT_POSITIONS[$position] ?? null;

        if ($type === null) {
            return null;
        }

        if (trim((string) $positionStatus) === self::VACANT_POSITION_STATUS) {
            return null;
        }

        $middleName = trim((string) $middleName);

        return [
            'type' => $type,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'middle_name' => $middleName !== '' && $middleName !== '-' ? $middleName : null,
            'position' => $position,
            'employment_status' => $this->nullableTrim($employmentStatus),
            'position_status' => $this->nullableTrim($positionStatus),
            'ambulatoriya_name' => $this->parseAmbulatoriyaName($department),
        ];
    }

    public function parseAmbulatoriyaName(?string $department): ?string
    {
        $department = trim((string) $department);

        if (preg_match('/Амбулаторія\s*№\s*\d+/u', $department, $matches) === 1) {
            return preg_replace('/\s+/u', ' ', $matches[0]);
        }

        return null;
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
