<?php

namespace Database\Factories;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\Brand;
use App\Models\License;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    protected $model = License::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productName = fake()->words(3, true);
        $productSlug = \Illuminate\Support\Str::slug($productName);

        return [
            'brand_id' => Brand::factory(),
            'customer_email' => fake()->safeEmail(),
            'customer_name' => fake()->name(),
            'product_name' => $productName,
            'product_slug' => $productSlug,
            'product_sku' => strtoupper(fake()->bothify('??-####')),
            'license_type' => fake()->randomElement(LicenseType::values()),
            'max_activations_per_instance' => ['site_url' => fake()->numberBetween(1, 10)],
            'current_activations' => 0,
            'expires_at' => fake()->dateTimeBetween('now', '+1 year'),
            'status' => LicenseStatus::ACTIVE,
            'metadata' => [
                'source' => fake()->randomElement(['web', 'api', 'import']),
                'notes' => fake()->sentence(),
            ],
        ];
    }

    /**
     * Indicate that the license is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'status' => LicenseStatus::EXPIRED,
        ]);
    }

    /**
     * Indicate that the license is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LicenseStatus::SUSPENDED,
        ]);
    }

    /**
     * Indicate that the license is perpetual.
     */
    public function perpetual(): static
    {
        return $this->state(fn (array $attributes) => [
            'license_type' => LicenseType::PERPETUAL->value,
            'expires_at' => null,
        ]);
    }
}
