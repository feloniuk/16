<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'director', 'warehouse_keeper']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:1000',
            'repair_master_id' => 'nullable|exists:repair_masters,id',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:room_inventory,id',
            'items.*.repair_description' => 'required|string|max:1000',
            'items.*.cost' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }

    /**
     * Get custom error messages for validator rules.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Необхідно додати принаймні один предмет на ремонт',
            'items.min' => 'Необхідно додати принаймні один предмет на ремонт',
            'items.*.equipment_id.required' => 'Виберіть обладнання',
            'items.*.equipment_id.exists' => 'Обладнання не знайдено',
            'items.*.repair_description.required' => 'Опишіть проблему для кожного предмета',
            'items.*.repair_description.max' => 'Опис не може бути більше 1000 символів',
            'items.*.cost.numeric' => 'Вартість має бути числом',
            'items.*.cost.max' => 'Вартість не може перевищувати 999999.99',
        ];
    }
}
