<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewLicenseRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'days' => ['required', 'integer', 'min:1', 'max:3650'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'days.required' => 'The number of days is required.',
            'days.integer' => 'The number of days must be an integer.',
            'days.min' => 'The number of days must be at least 1.',
            'days.max' => 'The number of days cannot exceed 3650 (10 years).',
        ];
    }

    /**
     * Get the number of days to renew.
     */
    public function getDays(): int
    {
        return (int) $this->input('days');
    }
}
