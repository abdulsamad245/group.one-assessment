<?php

/**
 * Activation API Tests
 *
 * Tests for Activation API endpoints covering:
 * - US3: End-user product can activate a license
 * - US5: End-user product or customer can deactivate a seat
 *
 * Includes comprehensive seat enforcement and instance type testing.
 *
 * @package Tests\Feature\Api\V1
 */

namespace Tests\Feature\Api\V1;

use App\Enums\ActivationStatus;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\Activation;
use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivationApiTest extends TestCase
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
        ]);

        $this->license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $this->licenseKey->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'Test Product',
            'product_slug' => 'test-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'status' => LicenseStatus::ACTIVE,
            'max_activations_per_instance' => ['site_url' => 3, 'machine_id' => 2],
            'current_activations' => 0,
            'expires_at' => now()->addYear(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | US3: End-user product can activate a license
    |--------------------------------------------------------------------------
    */

    public function test_product_can_activate_license_with_valid_key(): void
    {
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
            'device_identifier' => 'DEVICE-001',
            'device_name' => 'Production Server',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'instance_type',
                    'instance_value',
                    'status',
                    'activated_at',
                ],
            ]);

        $this->assertDatabaseHas('activations', [
            'license_key_id' => $this->licenseKey->id,
            'license_id' => $this->license->id,
            'instance_type' => 'site_url',
            'status' => ActivationStatus::ACTIVE->value,
        ]);

        // Verify activation count incremented
        $this->license->refresh();
        $this->assertEquals(1, $this->license->current_activations);
    }

    public function test_activation_returns_existing_activation_for_same_instance(): void
    {
        // First activation
        $firstResponse = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $firstResponse->assertStatus(201);
        $firstActivationId = $firstResponse->json('data.id');

        // Second activation for same instance should return existing
        $secondResponse = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $secondResponse->assertStatus(201);
        $secondActivationId = $secondResponse->json('data.id');

        $this->assertEquals($firstActivationId, $secondActivationId);

        // Should only have 1 activation
        $this->assertDatabaseCount('activations', 1);
    }

    public function test_activation_fails_with_invalid_license_key(): void
    {
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => 'INVALID-KEY-12345',
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_activation_fails_for_wrong_product(): void
    {
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'wrong-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_activation_fails_for_suspended_license(): void
    {
        $this->license->update(['status' => LicenseStatus::SUSPENDED]);

        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_activation_fails_for_expired_license(): void
    {
        $this->license->update(['expires_at' => now()->subDay()]);

        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_activation_fails_for_unconfigured_instance_type(): void
    {
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'unconfigured_type',
            'instance_value' => 'some-value',
        ]);

        $response->assertStatus(422);
    }

    /*
    |--------------------------------------------------------------------------
    | Seat Enforcement Tests
    |--------------------------------------------------------------------------
    */

    public function test_seat_limit_enforced_per_instance_type(): void
    {
        // Max 3 site_url activations configured
        $sites = [
            'https://site1.com',
            'https://site2.com',
            'https://site3.com',
        ];

        foreach ($sites as $site) {
            $response = $this->postJson('/api/v1/activations', [
                'license_key' => $this->licenseKey->key,
                'product_slug' => 'test-product',
                'instance_type' => 'site_url',
                'instance_value' => $site,
            ]);
            $response->assertStatus(201);
        }

        // 4th site should fail
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site4.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_different_instance_types_have_separate_seat_counts(): void
    {
        // Max 3 site_url and 2 machine_id configured
        // Use all site_url seats
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->postJson('/api/v1/activations', [
                'license_key' => $this->licenseKey->key,
                'product_slug' => 'test-product',
                'instance_type' => 'site_url',
                'instance_value' => "https://site{$i}.com",
            ]);
            $response->assertStatus(201);
        }

        // Should still be able to use machine_id seats
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'machine_id',
            'instance_value' => 'MACHINE-001',
        ]);

        $response->assertStatus(201);
    }

    /*
    |--------------------------------------------------------------------------
    | US5: End-user product or customer can deactivate a seat
    |--------------------------------------------------------------------------
    */

    public function test_product_can_deactivate_activation(): void
    {
        // Create an activation first
        $activation = Activation::factory()->create([
            'license_key_id' => $this->licenseKey->id,
            'license_id' => $this->license->id,
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
            'status' => ActivationStatus::ACTIVE,
        ]);

        $this->license->update(['current_activations' => 1]);

        $response = $this->postJson('/api/v1/deactivations', [
            'activation_id' => $activation->id,
        ]);

        $response->assertStatus(200);

        $activation->refresh();
        $this->assertEquals(ActivationStatus::INACTIVE, $activation->status);
        $this->assertNotNull($activation->deactivated_at);

        // Verify activation count decremented
        $this->license->refresh();
        $this->assertEquals(0, $this->license->current_activations);
    }

    public function test_deactivation_frees_seat_for_reuse(): void
    {
        // Max 1 seat for this test
        $this->license->update(['max_activations_per_instance' => ['site_url' => 1]]);

        // Activate first site
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site1.com',
        ]);
        $response->assertStatus(201);
        $activationId = $response->json('data.id');

        // Second site should fail
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site2.com',
        ]);
        $response->assertStatus(422);

        // Deactivate first site
        $this->postJson('/api/v1/deactivations', [
            'activation_id' => $activationId,
        ])->assertStatus(200);

        // Now second site should work
        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site2.com',
        ]);
        $response->assertStatus(201);
    }

    public function test_deactivation_fails_for_nonexistent_activation(): void
    {
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $response = $this->postJson('/api/v1/deactivations', [
            'activation_id' => $fakeUuid,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['activation_id']);
    }

    public function test_deactivation_requires_valid_uuid(): void
    {
        $response = $this->postJson('/api/v1/deactivations', [
            'activation_id' => 'invalid-uuid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['activation_id']);
    }

    /*
    |--------------------------------------------------------------------------
    | Activation Status Check Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_check_activation_status(): void
    {
        $response = $this->getJson('/api/v1/activations/status?' . http_build_query([
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.product_slug', 'test-product')
            ->assertJsonStructure([
                'data' => [
                    'valid',
                    'license_type',
                    'product_name',
                    'product_slug',
                    'entitlements',
                ],
            ]);
    }

    public function test_status_check_returns_seat_information(): void
    {
        // Create some activations
        for ($i = 1; $i <= 2; $i++) {
            $this->postJson('/api/v1/activations', [
                'license_key' => $this->licenseKey->key,
                'product_slug' => 'test-product',
                'instance_type' => 'site_url',
                'instance_value' => "https://site{$i}.com",
            ]);
        }

        $response = $this->getJson('/api/v1/activations/status?' . http_build_query([
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.entitlements.site_url.max_seats', 3)
            ->assertJsonPath('data.entitlements.site_url.used_seats', 2)
            ->assertJsonPath('data.entitlements.site_url.remaining_seats', 1);
    }

    public function test_status_check_returns_invalid_for_expired_license(): void
    {
        $this->license->update(['expires_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/activations/status?' . http_build_query([
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.valid', false);
    }

    public function test_status_check_returns_invalid_for_suspended_license(): void
    {
        $this->license->update(['status' => LicenseStatus::SUSPENDED]);

        $response = $this->getJson('/api/v1/activations/status?' . http_build_query([
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.valid', false);
    }

    public function test_perpetual_license_never_expires(): void
    {
        $this->license->update([
            'license_type' => LicenseType::PERPETUAL,
            'expires_at' => null,
        ]);

        $response = $this->postJson('/api/v1/activations', [
            'license_key' => $this->licenseKey->key,
            'product_slug' => 'test-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://example.com',
        ]);

        $response->assertStatus(201);
    }
}
