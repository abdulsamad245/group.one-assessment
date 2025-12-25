<?php

/**
 * License Key API Tests
 *
 * Tests for License Key API endpoints covering:
 * - US4: User can check license status
 *
 * @package Tests\Feature\Api\V1
 */

namespace Tests\Feature\Api\V1;

use App\Enums\ActivationStatus;
use App\Enums\LicenseKeyStatus;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\Activation;
use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseKeyApiTest extends TestCase
{
    use RefreshDatabase;

    protected Brand $brand;
    protected LicenseKey $licenseKey;
    protected License $license;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brand = Brand::factory()->create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        $this->licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->brand->id,
            'customer_email' => 'customer@example.com',
            'status' => LicenseKeyStatus::ACTIVE,
        ]);

        $this->license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $this->licenseKey->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'Test Product',
            'product_slug' => 'test-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'status' => LicenseStatus::ACTIVE,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | US4: User can check license status
    |--------------------------------------------------------------------------
    */

    public function test_user_can_check_license_key_status_by_key(): void
    {
        $response = $this->getJson("/api/v1/license-keys/key/{$this->licenseKey->key}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'status',
                    'licenses',
                ],
            ]);
    }

    public function test_license_key_response_includes_all_associated_licenses(): void
    {
        // Add a second license to the same key
        License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $this->licenseKey->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'Content AI',
            'product_slug' => 'content-ai',
            'status' => LicenseStatus::ACTIVE,
        ]);

        $response = $this->getJson("/api/v1/license-keys/key/{$this->licenseKey->key}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.licenses');
    }

    public function test_license_key_response_includes_entitlements(): void
    {
        $response = $this->getJson("/api/v1/license-keys/key/{$this->licenseKey->key}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'licenses' => [
                        '*' => [
                            'id',
                            'product_name',
                            'product_slug',
                            'status',
                            'license_type',
                            'expires_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_license_key_response_includes_activation_count(): void
    {
        // Create some activations
        Activation::factory()->count(2)->create([
            'license_key_id' => $this->licenseKey->id,
            'license_id' => $this->license->id,
            'instance_type' => 'site_url',
            'status' => ActivationStatus::ACTIVE,
        ]);

        $response = $this->getJson("/api/v1/license-keys/key/{$this->licenseKey->key}");

        $response->assertStatus(200)
            ->assertJsonPath('data.activations_count', 2);
    }

    public function test_returns_404_for_invalid_license_key(): void
    {
        $response = $this->getJson('/api/v1/license-keys/key/INVALID-KEY-12345');

        $response->assertStatus(404);
    }

    public function test_license_key_shows_valid_and_expired_licenses(): void
    {
        // Create an expired license
        License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $this->licenseKey->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'Expired Product',
            'product_slug' => 'expired-product',
            'status' => LicenseStatus::EXPIRED,
            'expires_at' => now()->subMonth(),
        ]);

        $response = $this->getJson("/api/v1/license-keys/key/{$this->licenseKey->key}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.licenses');
    }
}

