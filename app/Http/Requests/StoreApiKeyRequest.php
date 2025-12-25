<?php

namespace App\Http\Requests;

use App\DTOs\CreateApiKeyDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreApiKeyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ];
    }

    /**
     * Create a DTO from the validated request data.
     */
    public function createDTO(): CreateApiKeyDTO
    {
        $validated = $this->validated();

        return (new CreateApiKeyDTO())
            ->setBrandId($this->user()->brand_id)
            ->setName($validated['name'])
            ->setPermissions($validated['permissions'] ?? null);
    }
}
