<?php

/**
 * Multi-Tenancy Isolation Tests
 *
 * Tests ensuring complete brand isolation in the multi-tenant License Service.
 * Brands should not be able to access, modify, or view other brands' resources.
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

class MultiTenancyIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Brand $brandA;
    protected Brand $brandB;
    protected string $apiKeyA;
    protected string $apiKeyB;
    protected LicenseKey $licenseKeyA;
    protected LicenseKey $licenseKeyB;
    protected License $licenseA;
    protected License $licenseB;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Brand A (e.g., RankMath)
        $this->brandA = Brand::factory()->create([
            'name' => 'RankMath',
            'slug' => 'rankmath',
            'is_active' => true,
        ]);

        $plainKeyA = ApiKey::generate();
        ApiKey::create([
            'brand_id' => $this->brandA->id,
            'name' => 'RankMath API Key',
            'key' => ApiKey::hash($plainKeyA),
            'prefix' => ApiKey::extractPrefix($plainKeyA),
            'is_active' => true,
        ]);
        $this->apiKeyA = $plainKeyA;

        // Setup Brand B (e.g., WP Rocket)
        $this->brandB = Brand::factory()->create([
            'name' => 'WP Rocket',
            'slug' => 'wp-rocket',
            'is_active' => true,
        ]);

        $plainKeyB = ApiKey::generate();
        ApiKey::create([
            'brand_id' => $this->brandB->id,
            'name' => 'WP Rocket API Key',
            'key' => ApiKey::hash($plainKeyB),
            'prefix' => ApiKey::extractPrefix($plainKeyB),
            'is_active' => true,
        ]);
        $this->apiKeyB = $plainKeyB;

        // Create license keys and licenses for each brand
        $this->licenseKeyA = LicenseKey::factory()->create([
            'brand_id' => $this->brandA->id,
            'customer_email' => 'customer@example.com',
        ]);

        $this->licenseA = License::factory()->create([
            'brand_id' => $this->brandA->id,
            'license_key_id' => $this->licenseKeyA->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'RankMath Pro',
            'product_slug' => 'rankmath-pro',
        ]);

        $this->licenseKeyB = LicenseKey::factory()->create([
            'brand_id' => $this->brandB->id,
            'customer_email' => 'customer@example.com',
        ]);

        $this->licenseB = License::factory()->create([
            'brand_id' => $this->brandB->id,
            'license_key_id' => $this->licenseKeyB->id,
            'customer_email' => 'customer@example.com',
            'product_name' => 'WP Rocket',
            'product_slug' => 'wp-rocket',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | License Isolation Tests
    |--------------------------------------------------------------------------
    */

    public function test_brand_cannot_view_other_brands_license(): void
    {
        // Brand A tries to access Brand B's license
        $response = $this->getJson("/api/v1/licenses/{$this->licenseB->id}", [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(404);
    }

    public function test_brand_cannot_update_other_brands_license(): void
    {
        $response = $this->putJson("/api/v1/licenses/{$this->licenseB->id}", [
            'status' => LicenseStatus::SUSPENDED->value,
        ], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(404);

        // Verify license wasn't modified
        $this->licenseB->refresh();
        $this->assertEquals(LicenseStatus::ACTIVE, $this->licenseB->status);
    }

    public function test_brand_cannot_suspend_other_brands_license(): void
    {
        $response = $this->postJson("/api/v1/licenses/{$this->licenseB->id}/suspend", [], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(404);
    }

    public function test_brand_cannot_cancel_other_brands_license(): void
    {
        $response = $this->postJson("/api/v1/licenses/{$this->licenseB->id}/cancel", [], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(404);
    }

    public function test_brand_cannot_renew_other_brands_license(): void
    {
        $response = $this->postJson("/api/v1/licenses/{$this->licenseB->id}/renew", [
            'days' => 365,
        ], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | License List Isolation Tests
    |--------------------------------------------------------------------------
    */

    public function test_brand_only_sees_own_licenses_in_list(): void
    {
        $response = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(200);

        $licenseIds = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($this->licenseA->id, $licenseIds);
        $this->assertNotContains($this->licenseB->id, $licenseIds);
    }

    public function test_brand_b_only_sees_own_licenses(): void
    {
        $response = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => $this->apiKeyB,
        ]);

        $response->assertStatus(200);

        $licenseIds = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($this->licenseB->id, $licenseIds);
        $this->assertNotContains($this->licenseA->id, $licenseIds);
    }

    /*
    |--------------------------------------------------------------------------
    | License Creation Isolation Tests
    |--------------------------------------------------------------------------
    */

    public function test_license_created_by_brand_a_belongs_to_brand_a(): void
    {
        $response = $this->postJson('/api/v1/licenses', [
            'customer_email' => 'newcustomer@example.com',
            'customer_name' => 'New Customer',
            'product_name' => 'New Product',
            'product_slug' => 'new-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $response->assertStatus(201);

        $license = License::where('product_slug', 'new-product')->first();
        $this->assertEquals($this->brandA->id, $license->brand_id);
    }

    public function test_same_customer_gets_different_license_keys_per_brand(): void
    {
        $customerEmail = 'shared-customer@example.com';

        // Create license for Brand A
        $responseA = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Shared Customer',
            'product_name' => 'RankMath Premium',
            'product_slug' => 'rankmath-premium',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], [
            'X-API-Key' => $this->apiKeyA,
        ]);

        $responseA->assertStatus(201);
        $keyA = $responseA->json('data.license_key');

        // Create license for Brand B
        $responseB = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Shared Customer',
            'product_name' => 'WP Rocket Premium',
            'product_slug' => 'wp-rocket-premium',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], [
            'X-API-Key' => $this->apiKeyB,
        ]);

        $responseB->assertStatus(201);
        $keyB = $responseB->json('data.license_key');

        // Both should have generated new keys (different brands)
        $this->assertNotNull($keyA);
        $this->assertNotNull($keyB);
        $this->assertNotEquals($keyA, $keyB);

        // Verify license keys are associated with correct brands
        // Use withoutGlobalScopes to bypass brand filtering for this assertion
        $licenseKeys = LicenseKey::withoutGlobalScopes()
            ->where('customer_email', $customerEmail)
            ->get();
        $this->assertCount(2, $licenseKeys);
    }

    /*
    |--------------------------------------------------------------------------
    | API Key Security Tests
    |--------------------------------------------------------------------------
    */

    public function test_inactive_api_key_denied_access(): void
    {
        // Create inactive API key
        $inactiveKey = ApiKey::generate();
        ApiKey::create([
            'brand_id' => $this->brandA->id,
            'name' => 'Inactive Key',
            'key' => ApiKey::hash($inactiveKey),
            'prefix' => ApiKey::extractPrefix($inactiveKey),
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => $inactiveKey,
        ]);

        $response->assertStatus(401);
    }

    public function test_expired_api_key_denied_access(): void
    {
        $expiredKey = ApiKey::generate();
        ApiKey::create([
            'brand_id' => $this->brandA->id,
            'name' => 'Expired Key',
            'key' => ApiKey::hash($expiredKey),
            'prefix' => ApiKey::extractPrefix($expiredKey),
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => $expiredKey,
        ]);

        $response->assertStatus(401);
    }

    public function test_invalid_api_key_denied_access(): void
    {
        $response = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => 'completely-invalid-key',
        ]);

        $response->assertStatus(401);
    }

    public function test_missing_api_key_denied_access(): void
    {
        $response = $this->getJson('/api/v1/licenses');

        $response->assertStatus(401);
    }
}

