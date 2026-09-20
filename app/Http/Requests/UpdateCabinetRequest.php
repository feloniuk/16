<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCabinetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cabinet = $this->route('cabinet');

        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cabinets')->where('branch_id', $this->input('branch_id'))->ignore($cabinet),
            ],
            'ambulatoriya_id' => ['nullable', 'exists:ambulatorii,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
