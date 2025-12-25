<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeactivateLicenseRequest extends FormRequest
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
            'activation_id' => ['required', 'uuid', 'exists:activations,id'],
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
            'activation_id.required' => __('messages.activation-id-required'),
            'activation_id.uuid' => __('messages.activation-id-invalid'),
            'activation_id.exists' => __('messages.activation-not-found'),
        ];
    }

    /**
     * Create DeactivationDTO from request.
     */
    public function createDeactivationDTO(): \App\DTOs\DeactivationDTO
    {
        $dto = new \App\DTOs\DeactivationDTO();
        $dto->setActivationId($this->activation_id);

        return $dto;
    }
}
