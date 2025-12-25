<?php

/**
 * License API Tests
 *
 * Tests for License API endpoints covering:
 * - US1: Brand can provision a license
 * - US2: Brand can change license lifecycle (renew, suspend, resume, cancel)
 *
 * @package Tests\Feature\Api\V1
 */

namespace Tests\Feature\Api\V1;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\ApiKey;
use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithApiKey;

class LicenseApiTest extends TestCase
{
    use RefreshDatabase;
    use WithApiKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApiKey();
    }

    /*
    |--------------------------------------------------------------------------
    | US1: Brand can provision a license
    |--------------------------------------------------------------------------
    */

    public function test_brand_can_create_license_with_new_license_key(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'RankMath Pro',
            'product_slug' => 'rankmath-pro',
            'product_sku' => 'RM-PRO-001',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'license',
                    'license_key',
                ],
            ]);

        $this->assertNotNull($response->json('data.license_key'));

        // Verify license was created (customer_email is encrypted, so we check other fields)
        $this->assertDatabaseHas('licenses', [
            'product_slug' => 'rankmath-pro',
            'brand_id' => $this->testBrand->id,
        ]);

        // Verify the license has the correct customer email by loading it
        $license = License::where('product_slug', 'rankmath-pro')
            ->where('brand_id', $this->testBrand->id)
            ->first();
        $this->assertNotNull($license);
        $this->assertEquals('customer@example.com', $license->customer_email);
    }

    public function test_brand_can_add_license_to_existing_license_key(): void
    {
        // First license creates a license key
        $firstResponse = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'RankMath Pro',
            'product_slug' => 'rankmath-pro',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ]);

        $firstResponse->assertStatus(201);
        $firstLicenseKey = $firstResponse->json('data.license_key');

        // Second license for same customer should use existing key
        $secondResponse = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Content AI',
            'product_slug' => 'content-ai',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ]);

        $secondResponse->assertStatus(201);
        // Second license should not generate new key
        $this->assertNull($secondResponse->json('data.license_key'));

        // Both licenses should exist for this customer
        $this->assertDatabaseCount('license_keys', 1);
        $this->assertDatabaseCount('licenses', 2);
    }

    public function test_license_requires_valid_customer_email(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'invalid-email',
            'customer_name' => 'John Doe',
            'product_name' => 'Test Product',
            'product_slug' => 'test-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 1],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_email']);
    }

    public function test_license_requires_valid_product_slug_format(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Test Product',
            'product_slug' => 'Invalid Slug With Spaces',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 1],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_slug']);
    }

    public function test_license_requires_max_activations_per_instance(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Test Product',
            'product_slug' => 'test-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_activations_per_instance']);
    }

    public function test_brand_can_get_license_by_id(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
            'product_slug' => 'test-product',
        ]);

        $response = $this->getJsonWithApiKey("/api/v1/licenses/{$license->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $license->id)
            ->assertJsonPath('data.product_slug', 'test-product');
    }

    public function test_brand_can_list_own_licenses(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        License::factory()->count(3)->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
        ]);

        $response = $this->getJsonWithApiKey('/api/v1/licenses');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | US2: Brand can change license lifecycle
    |--------------------------------------------------------------------------
    */

    public function test_brand_can_renew_license(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
            'expires_at' => now()->addDays(30),
        ]);

        $originalExpiry = $license->expires_at;

        $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/renew", [
            'days' => 365,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::ACTIVE->value);

        $license->refresh();
        $this->assertTrue($license->expires_at->gt($originalExpiry));
    }

    public function test_brand_can_suspend_license(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
            'status' => LicenseStatus::ACTIVE,
        ]);

        $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/suspend");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::SUSPENDED->value);

        $license->refresh();
        $this->assertEquals(LicenseStatus::SUSPENDED, $license->status);
    }

    public function test_brand_can_resume_suspended_license(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
            'status' => LicenseStatus::SUSPENDED,
        ]);

        $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/resume");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::ACTIVE->value);

        $license->refresh();
        $this->assertEquals(LicenseStatus::ACTIVE, $license->status);
    }

    public function test_brand_can_cancel_license(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
            'status' => LicenseStatus::ACTIVE,
        ]);

        $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::REVOKED->value);

        $license->refresh();
        $this->assertEquals(LicenseStatus::REVOKED, $license->status);
    }

    public function test_renew_requires_valid_days_parameter(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => 'test@example.com',
        ]);

        $license = License::factory()->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => 'test@example.com',
        ]);

        $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/renew", [
            'days' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['days']);
    }

    public function test_returns_404_for_nonexistent_license(): void
    {
        $fakeUuid = '00000000-0000-0000-0000-000000000000';

        $response = $this->getJsonWithApiKey("/api/v1/licenses/{$fakeUuid}");

        $response->assertStatus(404);
    }

    public function test_api_key_required_for_license_endpoints(): void
    {
        $response = $this->getJson('/api/v1/licenses');

        $response->assertStatus(401);
    }

    public function test_supports_perpetual_license_type(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Lifetime Product',
            'product_slug' => 'lifetime-product',
            'license_type' => LicenseType::PERPETUAL->value,
            'max_activations_per_instance' => ['site_url' => 1],
            'expires_at' => null,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.license.license_type', LicenseType::PERPETUAL->value);
    }

    public function test_supports_multiple_instance_types_in_license(): void
    {
        $response = $this->postJsonWithApiKey('/api/v1/licenses', [
            'customer_email' => 'customer@example.com',
            'customer_name' => 'John Doe',
            'product_name' => 'Multi-Platform Product',
            'product_slug' => 'multi-platform',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => [
                'site_url' => 5,
                'machine_id' => 2,
                'host' => 3,
            ],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ]);

        $response->assertStatus(201);

        $license = License::where('product_slug', 'multi-platform')->first();
        $this->assertEquals(5, $license->max_activations_per_instance['site_url']);
        $this->assertEquals(2, $license->max_activations_per_instance['machine_id']);
        $this->assertEquals(3, $license->max_activations_per_instance['host']);
    }
}

