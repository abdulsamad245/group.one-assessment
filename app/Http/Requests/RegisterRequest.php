<?php

namespace App\Http\Requests;

use App\DTOs\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'brand_name' => ['required', 'string', 'max:255', 'unique:brands,name'],
        ];
    }

    /**
     * Create a DTO from the validated request data.
     */
    public function createDTO(): RegisterDTO
    {
        $validated = $this->validated();

        $slug = \Illuminate\Support\Str::slug($validated['brand_name']);

        return new RegisterDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            brandName: $validated['brand_name'],
            brandSlug: $slug
        );
    }
}
