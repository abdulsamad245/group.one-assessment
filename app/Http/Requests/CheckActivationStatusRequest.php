<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckActivationStatusRequest extends FormRequest
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
            'license_key' => ['required', 'string'],
            'product_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
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
            'license_key.required' => __('messages.license-key-required'),
            'product_slug.required' => __('messages.product-slug-required'),
        ];
    }

    /**
     * Create CheckActivationStatusDTO from request.
     */
    public function createCheckStatusDTO(): \App\DTOs\CheckActivationStatusDTO
    {
        $dto = new \App\DTOs\CheckActivationStatusDTO();
        $dto->setLicenseKey($this->license_key)
            ->setProductSlug($this->product_slug);

        return $dto;
    }
}
