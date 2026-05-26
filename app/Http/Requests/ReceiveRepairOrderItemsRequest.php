<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReceiveRepairOrderItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'warehouse_keeper']);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['required', 'integer', 'exists:repair_order_items,id'],
            'returned_at' => ['required', 'date'],
            'return_document_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_ids.required' => 'Оберіть хоча б одну позицію',
            'item_ids.min' => 'Оберіть хоча б одну позицію',
            'returned_at.required' => 'Вкажіть дату отримання',
            'returned_at.date' => 'Некоректна дата',
        ];
    }
}
