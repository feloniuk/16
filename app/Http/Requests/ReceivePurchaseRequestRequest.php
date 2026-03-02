<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceivePurchaseRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'warehouse_keeper']);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_request_item_id' => ['required', 'integer', 'exists:purchase_request_items,id'],
            'items.*.actual_quantity' => ['required', 'integer', 'min:1'],
            'items.*.action' => ['required', 'in:update_existing,create_new,link_to_existing'],
            'items.*.existing_inventory_id' => ['required_if:items.*.action,link_to_existing', 'nullable', 'integer', 'exists:room_inventory,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Виберіть принаймні один товар',
            'items.min' => 'Виберіть принаймні один товар',
            'items.*.purchase_request_item_id.required' => 'ID товару обов\'язковий',
            'items.*.purchase_request_item_id.exists' => 'Товар не знайдено',
            'items.*.actual_quantity.required' => 'Введіть кількість товару',
            'items.*.actual_quantity.min' => 'Кількість повинна бути мінімум 1',
            'items.*.action.required' => 'Оберіть дію',
            'items.*.action.in' => 'Невірна дія',
            'items.*.existing_inventory_id.required_if' => 'Оберіть товар на складі',
            'items.*.existing_inventory_id.exists' => 'Товар на складі не знайдено',
        ];
    }
}
