<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveInventoryAuditRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'staff' => ['nullable', 'array'],
            'staff.*.doctor' => ['nullable', 'string', 'max:255'],
            'staff.*.nurse' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.room_inventory_id' => ['nullable', 'integer'],
            'items.*.equipment_type' => ['nullable', 'string', 'max:100'],
            'items.*.brand' => ['nullable', 'string', 'max:100'],
            'items.*.model' => ['nullable', 'string', 'max:100'],
            'items.*.serial_number' => ['nullable', 'string', 'max:100'],
            'items.*.inventory_number' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'Оберіть філію',
            'branch_id.exists' => 'Філію не знайдено',
            'room_number.required' => 'Вкажіть номер кабінету',
            'room_number.max' => 'Номер кабінету задовгий (до 50 символів)',
            'staff.*.doctor.max' => 'ПІБ лікаря задовге',
            'staff.*.nurse.max' => 'ПІБ медсестри задовге',
            'items.*.equipment_type.max' => 'Назва обладнання задовга',
            'items.*.quantity.integer' => 'Кількість має бути цілим числом',
            'items.*.quantity.min' => 'Кількість не може бути від\'ємною',
        ];
    }
}
