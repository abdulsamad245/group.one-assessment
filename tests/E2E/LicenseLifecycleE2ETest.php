<?php

namespace Tests\E2E;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-End License Lifecycle Tests
 *
 * Complete end-to-end tests simulating real-world multi-brand license scenarios
 * including provisioning, activation, status checks, deactivation, and customer queries.
 */
class LicenseLifecycleE2ETest extends TestCase
{
    use RefreshDatabase;

    protected Brand $brandA;
    protected Brand $brandB;
    protected string $apiKeyA;
    protected string $apiKeyB;

    protected function setUp(): void
    {
        parent::setUp();

        // Brand A - Primary SEO product brand
        $this->brandA = Brand::factory()->create(['name' => 'Brand A', 'slug' => 'brand-a', 'is_active' => true]);
        $keyA = ApiKey::generate();
        ApiKey::create(['brand_id' => $this->brandA->id, 'name' => 'Brand A Key', 'key' => ApiKey::hash($keyA), 'prefix' => ApiKey::extractPrefix($keyA), 'is_active' => true]);
        $this->apiKeyA = $keyA;

        // Brand B - Performance optimization brand
        $this->brandB = Brand::factory()->create(['name' => 'Brand B', 'slug' => 'brand-b', 'is_active' => true]);
        $keyB = ApiKey::generate();
        ApiKey::create(['brand_id' => $this->brandB->id, 'name' => 'Brand B Key', 'key' => ApiKey::hash($keyB), 'prefix' => ApiKey::extractPrefix($keyB), 'is_active' => true]);
        $this->apiKeyB = $keyB;
    }

    public function test_customer_purchases_multiple_products_across_brands(): void
    {
        $customerEmail = 'customer@example.com';

        // Customer purchases first product from Brand A - generates license key
        $response1 = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'John Doe',
            'product_name' => 'SEO Pro',
            'product_slug' => 'seo-pro',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA]);

        $response1->assertStatus(201);
        $licenseKeyA = $response1->json('data.license_key');
        $this->assertNotNull($licenseKeyA, 'First purchase should generate new license key');

        // Customer purchases addon from Brand A - uses same license key
        $response2 = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'John Doe',
            'product_name' => 'AI Content Writer',
            'product_slug' => 'ai-content',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA]);

        $response2->assertStatus(201);
        $this->assertNull($response2->json('data.license_key'), 'Addon should use existing key');

        // Customer purchases from Brand B - generates new license key
        $response3 = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'John Doe',
            'product_name' => 'Cache Optimizer',
            'product_slug' => 'cache-optimizer',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyB]);

        $response3->assertStatus(201);
        $licenseKeyB = $response3->json('data.license_key');
        $this->assertNotNull($licenseKeyB, 'Different brand should generate new key');
        $this->assertNotEquals($licenseKeyA, $licenseKeyB, 'Keys should be different per brand');
    }

    public function test_complete_activation_and_deactivation_flow(): void
    {
        $customerEmail = 'activate@example.com';

        // Provision license
        $provision = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Jane Doe',
            'product_name' => 'Premium Plugin',
            'product_slug' => 'premium-plugin',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 2],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA]);

        $licenseKey = $provision->json('data.license_key');

        // Activate on first site
        $act1 = $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'premium-plugin',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site1.example.com',
        ]);
        $act1->assertStatus(201);
        $activationId1 = $act1->json('data.id');

        // Activate on second site
        $act2 = $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'premium-plugin',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site2.example.com',
        ]);
        $act2->assertStatus(201);

        // Third activation should fail (seat limit)
        $act3 = $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'premium-plugin',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site3.example.com',
        ]);
        $act3->assertStatus(422);

        // Deactivate first site
        $this->postJson('/api/v1/deactivations', ['activation_id' => $activationId1])->assertStatus(200);

        // Now third activation should succeed
        $act3Retry = $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'premium-plugin',
            'instance_type' => 'site_url',
            'instance_value' => 'https://site3.example.com',
        ]);
        $act3Retry->assertStatus(201);
    }

    public function test_license_status_check_and_validation(): void
    {
        $customerEmail = 'status@example.com';

        // Provision license
        $provision = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Status Check User',
            'product_name' => 'Status Product',
            'product_slug' => 'status-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 5],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA]);

        $licenseKey = $provision->json('data.license_key');

        // Check status - should be valid
        $status = $this->getJson("/api/v1/activations/status?license_key={$licenseKey}&product_slug=status-product");
        $status->assertStatus(200)->assertJsonPath('data.valid', true);

        // Activate and check status includes seat info
        $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'status-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://mysite.com',
        ]);

        $statusWithSeats = $this->getJson("/api/v1/activations/status?license_key={$licenseKey}&product_slug=status-product");
        $statusWithSeats->assertStatus(200)
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.entitlements.site_url.used_seats', 1);
    }

    public function test_brand_lifecycle_management_suspend_resume_renew_cancel(): void
    {
        $customerEmail = 'lifecycle@example.com';

        // Provision license
        $provision = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Lifecycle User',
            'product_name' => 'Lifecycle Product',
            'product_slug' => 'lifecycle-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA]);

        $licenseId = $provision->json('data.license.id');
        $licenseKey = $provision->json('data.license_key');

        // Activate first
        $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'lifecycle-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://lifecycle.example.com',
        ])->assertStatus(201);

        // Suspend license
        $this->postJson("/api/v1/licenses/{$licenseId}/suspend", [], ['X-API-Key' => $this->apiKeyA])
            ->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::SUSPENDED->value);

        // Activation should fail while suspended
        $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'lifecycle-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://another.example.com',
        ])->assertStatus(422);

        // Resume license
        $this->postJson("/api/v1/licenses/{$licenseId}/resume", [], ['X-API-Key' => $this->apiKeyA])
            ->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::ACTIVE->value);

        // Renew license
        $this->postJson("/api/v1/licenses/{$licenseId}/renew", ['days' => 365], ['X-API-Key' => $this->apiKeyA])
            ->assertStatus(200);

        // Cancel license
        $this->postJson("/api/v1/licenses/{$licenseId}/cancel", [], ['X-API-Key' => $this->apiKeyA])
            ->assertStatus(200)
            ->assertJsonPath('data.status', LicenseStatus::REVOKED->value);
    }

    public function test_brand_only_sees_own_licenses_for_customer(): void
    {
        $customerEmail = 'multi-brand@example.com';

        // Purchase from Brand A
        $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Multi Brand User',
            'product_name' => 'Brand A Product',
            'product_slug' => 'brand-a-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 3],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyA])->assertStatus(201);

        // Purchase from Brand B
        $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Multi Brand User',
            'product_name' => 'Brand B Product',
            'product_slug' => 'brand-b-product',
            'license_type' => LicenseType::SUBSCRIPTION->value,
            'max_activations_per_instance' => ['site_url' => 2],
            'expires_at' => now()->addYear()->toDateTimeString(),
        ], ['X-API-Key' => $this->apiKeyB])->assertStatus(201);

        // Query licenses for customer using Brand A's API key
        // Brand A should only see its own license (multi-tenant isolation)
        $responseA = $this->getJson("/api/v1/customers/licenses?email={$customerEmail}", ['X-API-Key' => $this->apiKeyA]);

        $responseA->assertStatus(200)
            ->assertJsonPath('data.customer_email', $customerEmail)
            ->assertJsonPath('data.total_licenses', 1)
            ->assertJsonCount(1, 'data.brands');

        // Query licenses for customer using Brand B's API key
        // Brand B should only see its own license (multi-tenant isolation)
        $responseB = $this->getJson("/api/v1/customers/licenses?email={$customerEmail}", ['X-API-Key' => $this->apiKeyB]);

        $responseB->assertStatus(200)
            ->assertJsonPath('data.customer_email', $customerEmail)
            ->assertJsonPath('data.total_licenses', 1)
            ->assertJsonCount(1, 'data.brands');
    }

    public function test_perpetual_license_never_expires(): void
    {
        $customerEmail = 'perpetual@example.com';

        // Provision perpetual license
        $provision = $this->postJson('/api/v1/licenses', [
            'customer_email' => $customerEmail,
            'customer_name' => 'Perpetual User',
            'product_name' => 'Lifetime Product',
            'product_slug' => 'lifetime-product',
            'license_type' => LicenseType::PERPETUAL->value,
            'max_activations_per_instance' => ['site_url' => 10],
        ], ['X-API-Key' => $this->apiKeyA]);

        $provision->assertStatus(201);
        $licenseKey = $provision->json('data.license_key');

        // Activate - should work without expiry
        $this->postJson('/api/v1/activations', [
            'license_key' => $licenseKey,
            'product_slug' => 'lifetime-product',
            'instance_type' => 'site_url',
            'instance_value' => 'https://perpetual.example.com',
        ])->assertStatus(201);

        // Status should be valid
        $this->getJson("/api/v1/activations/status?license_key={$licenseKey}&product_slug=lifetime-product")
            ->assertStatus(200)
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.license_type', LicenseType::PERPETUAL->value);
    }
}

