<?php

namespace App\Http\Requests;

use App\DTOs\UpdateLicenseDTO;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLicenseRequest extends FormRequest
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
            'customer_email' => ['sometimes', 'email', 'max:255'],
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'product_name' => ['sometimes', 'string', 'max:255'],
            'product_sku' => ['nullable', 'string', 'max:255'],
            'license_type' => ['sometimes', Rule::in(LicenseType::values())],
            'max_activations' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(LicenseStatus::values())],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Create DTO from request.
     */
    public function updateLicenseDTO(): UpdateLicenseDTO
    {
        $dto = new UpdateLicenseDTO();

        if ($this->has('customer_email')) {
            $dto->setCustomerEmail($this->customer_email);
        }
        if ($this->has('customer_name')) {
            $dto->setCustomerName($this->customer_name);
        }
        if ($this->has('product_name')) {
            $dto->setProductName($this->product_name);
        }
        if ($this->has('product_sku')) {
            $dto->setProductSku($this->product_sku);
        }
        if ($this->has('license_type')) {
            $dto->setLicenseType($this->license_type);
        }
        if ($this->has('max_activations')) {
            $dto->setMaxActivations($this->max_activations);
        }
        if ($this->has('expires_at')) {
            $dto->setExpiresAt($this->expires_at);
        }
        if ($this->has('status')) {
            $dto->setStatus($this->status);
        }
        if ($this->has('metadata')) {
            $dto->setMetadata($this->metadata);
        }

        return $dto;
    }
}
