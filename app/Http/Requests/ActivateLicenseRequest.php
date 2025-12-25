<?php

namespace App\Http\Requests;

use App\DTOs\CreateActivationDTO;
use Illuminate\Foundation\Http\FormRequest;

class ActivateLicenseRequest extends FormRequest
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
            'instance_type' => ['required', 'string', 'max:50'],
            'instance_value' => ['required', 'string', 'max:255'],
            'device_identifier' => ['nullable', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);
    }

    /**
     * Create DTO from request.
     */
    public function createActivationDTO(): CreateActivationDTO
    {
        $dto = new CreateActivationDTO();
        $dto->setLicenseKey($this->license_key)
            ->setProductSlug($this->product_slug)
            ->setInstanceType($this->instance_type)
            ->setInstanceValue($this->instance_value)
            ->setDeviceIdentifier($this->device_identifier)
            ->setDeviceName($this->device_name)
            ->setIpAddress($this->ip_address)
            ->setUserAgent($this->user_agent)
            ->setMetadata($this->metadata);

        return $dto;
    }
}
