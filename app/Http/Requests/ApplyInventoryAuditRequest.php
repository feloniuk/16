<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApplyInventoryAuditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Рішення приходять одним JSON-полем, щоб великий обхід не впирався в max_input_vars.
     */
    protected function prepareForValidation(): void
    {
        $decisions = $this->input('decisions');

        if (is_string($decisions)) {
            $this->merge(['decisions' => json_decode($decisions, true)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'decisions' => ['nullable', 'array'],
            'decisions.*' => ['string', 'regex:/^(confirm|update|move|create|skip|keep|remove|match:\d+)$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decisions.array' => 'Некоректний формат рішень звірки',
            'decisions.*.regex' => 'Невідома дія у рішеннях звірки',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function decisions(): array
    {
        return $this->validated('decisions') ?? [];
    }
}
