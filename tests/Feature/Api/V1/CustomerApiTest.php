<?php

/**
 * Customer API Tests
 *
 * Tests for Customer API endpoints covering:
 * - US6: Brands can list licenses by customer email across all brands
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

class CustomerApiTest extends TestCase
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
    | US6: Brands can list licenses by customer email across all brands
    |--------------------------------------------------------------------------
    */

    public function test_brand_can_list_licenses_by_customer_email(): void
    {
        $customerEmail = 'john@example.com';

        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => $customerEmail,
        ]);

        License::factory()->count(3)->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey->id,
            'customer_email' => $customerEmail,
        ]);

        $response = $this->getJsonWithApiKey('/api/v1/customers/licenses?' . http_build_query([
            'email' => $customerEmail,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'customer_email',
                    'total_licenses',
                    'active_licenses',
                    'total_activations',
                    'brands',
                    'license_key',
                ],
            ])
            ->assertJsonPath('data.customer_email', $customerEmail)
            ->assertJsonPath('data.total_licenses', 3);
    }

    public function test_brand_only_sees_own_licenses_for_customer(): void
    {
        $customerEmail = 'cross-brand@example.com';

        // Create another brand
        $secondBrand = Brand::factory()->create([
            'name' => 'Second Brand',
            'slug' => 'second-brand',
            'is_active' => true,
        ]);

        // License key and licenses for first brand (the test brand)
        $licenseKey1 = LicenseKey::factory()->create([
            'brand_id' => $this->testBrand->id,
            'customer_email' => $customerEmail,
        ]);

        License::factory()->count(2)->create([
            'brand_id' => $this->testBrand->id,
            'license_key_id' => $licenseKey1->id,
            'customer_email' => $customerEmail,
        ]);

        // License key and licenses for second brand (should NOT be visible)
        $licenseKey2 = LicenseKey::factory()->create([
            'brand_id' => $secondBrand->id,
            'customer_email' => $customerEmail,
        ]);

        License::factory()->create([
            'brand_id' => $secondBrand->id,
            'license_key_id' => $licenseKey2->id,
            'customer_email' => $customerEmail,
        ]);

        $response = $this->getJsonWithApiKey('/api/v1/customers/licenses?' . http_build_query([
            'email' => $customerEmail,
        ]));

        // Brand should only see its own licenses (2), not the other brand's license
        $response->assertStatus(200)
            ->assertJsonPath('data.total_licenses', 2)
            ->assertJsonCount(1, 'data.brands');
    }

    public function test_returns_empty_result_for_unknown_email(): void
    {
        $response = $this->getJsonWithApiKey('/api/v1/customers/licenses?' . http_build_query([
            'email' => 'unknown@example.com',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.total_licenses', 0)
            ->assertJsonPath('data.active_licenses', 0);
    }

    public function test_requires_valid_email_format(): void
    {
        $response = $this->getJsonWithApiKey('/api/v1/customers/licenses?' . http_build_query([
            'email' => 'invalid-email',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_requires_email_parameter(): void
    {
        $response = $this->getJsonWithApiKey('/api/v1/customers/licenses');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_requires_api_key_authentication(): void
    {
        $response = $this->getJson('/api/v1/customers/licenses?' . http_build_query([
            'email' => 'test@example.com',
        ]));

        $response->assertStatus(401);
    }
}

