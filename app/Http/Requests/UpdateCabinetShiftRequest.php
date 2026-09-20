<?php

namespace App\Http\Requests;

use App\Models\MedicalStaff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCabinetShiftRequest extends FormRequest
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
        return [
            'doctor_id' => [
                'nullable',
                Rule::exists('medical_staff', 'id')->where('type', MedicalStaff::TYPE_DOCTOR),
            ],
            'nurse_id' => [
                'nullable',
                Rule::exists('medical_staff', 'id')->where('type', MedicalStaff::TYPE_NURSE),
            ],
        ];
    }
}
