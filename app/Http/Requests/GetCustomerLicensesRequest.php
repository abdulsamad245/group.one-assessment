<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCustomerLicensesRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
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
            'email.required' => __('messages.email-required'),
            'email.email' => __('messages.email-invalid'),
        ];
    }

    /**
     * Create GetCustomerLicensesDTO from request.
     */
    public function createCustomerLicensesDTO(): \App\DTOs\GetCustomerLicensesDTO
    {
        $dto = new \App\DTOs\GetCustomerLicensesDTO();
        $dto->setEmail($this->email);

        return $dto;
    }
}
