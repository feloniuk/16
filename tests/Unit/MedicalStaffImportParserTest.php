<?php

namespace Tests\Unit;

use App\Models\MedicalStaff;
use App\Services\MedicalStaffImportParser;
use PHPUnit\Framework\TestCase;

class MedicalStaffImportParserTest extends TestCase
{
    private MedicalStaffImportParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new MedicalStaffImportParser;
    }

    public function test_parses_a_family_doctor_row(): void
    {
        $result = $this->parser->parseRow(
            lastName: 'Щербина',
            firstName: 'Любов',
            middleName: 'Василівна',
            employmentStatus: 'основний працівник',
            position: 'лікар загальної практики - сімейний лікар',
            positionStatus: 'зайнята',
            department: 'Амбулаторія № 1',
        );

        $this->assertNotNull($result);
        $this->assertSame(MedicalStaff::TYPE_DOCTOR, $result['type']);
        $this->assertSame('Щербина', $result['last_name']);
        $this->assertSame('Амбулаторія № 1', $result['ambulatoriya_name']);
    }

    public function test_parses_a_family_nurse_row(): void
    {
        $result = $this->parser->parseRow(
            lastName: 'Волкова',
            firstName: 'Лариса',
            middleName: 'Костянтинівна',
            employmentStatus: 'основний працівник',
            position: 'сестра медична загальної практики - сімейна медицина',
            positionStatus: 'зайнята',
            department: 'Амбулаторія № 1',
        );

        $this->assertNotNull($result);
        $this->assertSame(MedicalStaff::TYPE_NURSE, $result['type']);
    }

    public function test_skips_irrelevant_positions(): void
    {
        $result = $this->parser->parseRow(
            lastName: 'Головач',
            firstName: 'Ніна',
            middleName: 'Валеріївна',
            employmentStatus: 'основний працівник',
            position: 'сестра медична (брат медичний)',
            positionStatus: 'зайнята',
            department: 'Процедурний кабінет амб.№1-5',
        );

        $this->assertNull($result);
    }

    public function test_skips_vacant_positions(): void
    {
        $result = $this->parser->parseRow(
            lastName: 'Гораш',
            firstName: 'Альона',
            middleName: 'Ігорівна',
            employmentStatus: 'тимчасово відсутній (декретна відпустка, тощо)',
            position: 'сестра медична загальної практики - сімейна медицина',
            positionStatus: 'вільна тимчасово',
            department: 'Амбулаторія № 1',
        );

        $this->assertNull($result);
    }

    public function test_skips_rows_without_a_name(): void
    {
        $result = $this->parser->parseRow(
            lastName: '',
            firstName: '',
            middleName: null,
            employmentStatus: 'основний працівник',
            position: 'лікар загальної практики - сімейний лікар',
            positionStatus: 'зайнята',
            department: 'Амбулаторія № 1',
        );

        $this->assertNull($result);
    }

    public function test_dash_middle_name_is_normalized_to_null(): void
    {
        $result = $this->parser->parseRow(
            lastName: 'Зайада',
            firstName: 'Халдун',
            middleName: '-',
            employmentStatus: 'основний працівник',
            position: 'лікар загальної практики - сімейний лікар',
            positionStatus: 'зайнята',
            department: 'Амбулаторія № 1',
        );

        $this->assertNotNull($result);
        $this->assertNull($result['middle_name']);
    }

    public function test_parses_ambulatoriya_name_with_extra_whitespace(): void
    {
        $name = $this->parser->parseAmbulatoriyaName('Амбулаторія   №  10');

        $this->assertSame('Амбулаторія № 10', $name);
    }

    public function test_returns_null_when_department_is_not_an_ambulatoriya(): void
    {
        $name = $this->parser->parseAmbulatoriyaName('Адміністративно-управлінський персонал');

        $this->assertNull($name);
    }
}
