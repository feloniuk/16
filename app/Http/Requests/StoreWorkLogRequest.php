<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'work_type' => 'required|in:inventory_transfer,cartridge_replacement,repair_sent,repair_returned,manual',
            'description' => 'required|string|max:1000',
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
            'performed_at' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'work_type.required' => 'Оберіть тип роботи',
            'work_type.in' => 'Невірний тип роботи',
            'description.required' => 'Введіть опис роботи',
            'description.max' => 'Опис не може бути більше 1000 символів',
            'branch_id.required' => 'Оберіть філіал',
            'branch_id.exists' => 'Філіал не знайдено',
            'room_number.required' => 'Введіть номер кабінету',
            'room_number.max' => 'Номер кабінету не може бути більше 50 символів',
            'performed_at.required' => 'Вкажіть дату виконання',
            'performed_at.date' => 'Невірний формат дати',
            'notes.max' => 'Примітки не можуть бути більше 2000 символів',
        ];
    }
}
