<?php

namespace App\Http\Requests;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLicenseRequest extends FormRequest
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
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'product_sku' => ['nullable', 'string', 'max:255'],
            'license_type' => ['required', Rule::in(LicenseType::values())],
            'max_activations_per_instance' => ['required', 'array'],
            'max_activations_per_instance.*' => ['required', 'integer', 'min:1', 'max:1000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'status' => [Rule::in(LicenseStatus::values())],
            'metadata' => ['nullable', 'array'],
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
            'expires_at.after' => __('messages.invalid-date'),
        ];
    }

    /**
     * Create DTO from request.
     */
    public function createLicenseDTO(): \App\DTOs\CreateLicenseDTO
    {
        $dto = new \App\DTOs\CreateLicenseDTO();
        $dto->setBrandId($this->input('authenticated_brand_id'))
            ->setCustomerEmail($this->customer_email)
            ->setCustomerName($this->customer_name)
            ->setProductName($this->product_name)
            ->setProductSlug($this->product_slug)
            ->setProductSku($this->product_sku)
            ->setLicenseType($this->license_type)
            ->setMaxActivationsPerInstance($this->max_activations_per_instance)
            ->setExpiresAt($this->expires_at)
            ->setStatus($this->status)
            ->setMetadata($this->metadata);

        return $dto;
    }
}
