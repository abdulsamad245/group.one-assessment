<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug from name if name is being updated but slug is not provided
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brandId = $this->route('brand');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brandId)],
            'slug' => ['sometimes', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('brands', 'slug')->ignore($brandId)],
            'description' => ['nullable', 'string'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['boolean'],
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
            'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
        ];
    }

    /**
     * Create UpdateBrandDTO from request.
     */
    public function createUpdateBrandDTO(): \App\DTOs\UpdateBrandDTO
    {
        $dto = new \App\DTOs\UpdateBrandDTO();

        if ($this->has('name')) {
            $dto->setName($this->name);
        }
        if ($this->has('slug')) {
            $dto->setSlug($this->slug);
        }
        if ($this->has('description')) {
            $dto->setDescription($this->description);
        }
        if ($this->has('contact_email')) {
            $dto->setContactEmail($this->contact_email);
        }
        if ($this->has('website')) {
            $dto->setWebsite($this->website);
        }
        if ($this->has('settings')) {
            $dto->setSettings($this->settings);
        }
        if ($this->has('is_active')) {
            $dto->setIsActive($this->is_active);
        }

        return $dto;
    }
}
