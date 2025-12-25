<?php

namespace Database\Factories;

use App\Enums\LicenseKeyStatus;
use App\Models\LicenseKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LicenseKey>
 */
class LicenseKeyFactory extends Factory
{
    protected $model = LicenseKey::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = $this->generateLicenseKey();

        return [
            'brand_id' => \App\Models\Brand::factory(),
            'customer_email' => fake()->safeEmail(),
            'key' => $key,
            'key_hash' => hash('sha256', $key),
            'status' => LicenseKeyStatus::ACTIVE,
            'expires_at' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }

    /**
     * Generate a formatted license key.
     * Format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX (5 groups of 5 characters)
     */
    private function generateLicenseKey(): string
    {
        $key = strtoupper(Str::random(25));
        return implode('-', str_split($key, 5));
    }

    /**
     * Indicate that the license key is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LicenseKeyStatus::REVOKED,
        ]);
    }

    /**
     * Indicate that the license key is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LicenseKeyStatus::EXPIRED,
            'expires_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }
}
