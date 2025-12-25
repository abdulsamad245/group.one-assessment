<?php

namespace Database\Factories;

use App\Enums\ActivationStatus;
use App\Models\Activation;
use App\Models\LicenseKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activation>
 */
class ActivationFactory extends Factory
{
    protected $model = Activation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $instanceType = fake()->randomElement(['site_url', 'host', 'machine_id']);

        return [
            'license_key_id' => LicenseKey::factory(),
            'license_id' => \App\Models\License::factory(),
            'instance_type' => $instanceType,
            'instance_value' => match ($instanceType) {
                'site_url' => 'https://' . fake()->domainName(),
                'host' => fake()->domainWord() . '.server.local',
                'machine_id' => strtoupper(fake()->bothify('MACHINE-####-????')),
                default => fake()->uuid(),
            },
            'device_identifier' => strtoupper(fake()->bothify('??-####-####')),
            'device_name' => fake()->randomElement([
                'John\'s MacBook Pro',
                'Office Desktop',
                'Development Server',
                'Production Server',
            ]),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'status' => ActivationStatus::ACTIVE,
            'activated_at' => now(),
            'deactivated_at' => null,
            'last_checked_at' => now(),
            'metadata' => [
                'os' => fake()->randomElement(['Windows', 'macOS', 'Linux']),
                'version' => fake()->semver(),
            ],
        ];
    }

    /**
     * Indicate that the activation is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ActivationStatus::INACTIVE,
            'deactivated_at' => now(),
        ]);
    }
}
