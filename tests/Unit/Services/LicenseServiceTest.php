<?php

namespace Tests\Unit\Services;

use App\DTOs\CreateLicenseDTO;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use App\Repositories\LicenseKeyRepository;
use App\Repositories\LicenseRepository;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LicenseService $service;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LicenseService::class);
        $this->brand = Brand::factory()->create();
    }

    /** @test */
    public function creates_license_with_new_license_key_for_new_customer(): void
    {
        $dto = new CreateLicenseDTO();
        $dto->setBrandId($this->brand->id)
            ->setCustomerEmail('new@example.com')
            ->setCustomerName('New Customer')
            ->setProductName('Test Product')
            ->setProductSlug('test-product')
            ->setLicenseType(LicenseType::SUBSCRIPTION->value)
            ->setMaxActivationsPerInstance(['site_url' => 5])
            ->setExpiresAt(now()->addYear()->toDateTimeString());

        $result = $this->service->createLicense($dto);

        $this->assertArrayHasKey('license', $result);
        $this->assertArrayHasKey('license_key', $result);
        $this->assertNotNull($result['license_key']);
        $this->assertDatabaseCount('license_keys', 1);
        $this->assertDatabaseCount('licenses', 1);
    }

    /** @test */
    public function uses_existing_license_key_for_same_customer(): void
    {
        $licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->brand->id,
            'customer_email' => 'existing@example.com',
        ]);

        $dto = new CreateLicenseDTO();
        $dto->setBrandId($this->brand->id)
            ->setCustomerEmail('existing@example.com')
            ->setCustomerName('Existing')
            ->setProductName('Product 2')
            ->setProductSlug('product-2')
            ->setLicenseType(LicenseType::SUBSCRIPTION->value)
            ->setMaxActivationsPerInstance(['site_url' => 3]);

        $result = $this->service->createLicense($dto);

        $this->assertNull($result['license_key']);
        $this->assertDatabaseCount('license_keys', 1);
    }

    /** @test */
    public function suspends_license(): void
    {
        $licenseKey = LicenseKey::factory()->create(['brand_id' => $this->brand->id]);
        $license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $licenseKey->id,
            'status' => LicenseStatus::ACTIVE,
        ]);

        $result = $this->service->suspendLicense($license);

        $this->assertEquals(LicenseStatus::SUSPENDED, $result->status);
    }

    /** @test */
    public function reactivates_suspended_license(): void
    {
        $licenseKey = LicenseKey::factory()->create(['brand_id' => $this->brand->id]);
        $license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $licenseKey->id,
            'status' => LicenseStatus::SUSPENDED,
        ]);

        $result = $this->service->reactivateLicense($license);

        $this->assertEquals(LicenseStatus::ACTIVE, $result->status);
    }

    /** @test */
    public function renews_license_extending_expiry(): void
    {
        $licenseKey = LicenseKey::factory()->create(['brand_id' => $this->brand->id]);
        $license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $licenseKey->id,
            'expires_at' => now()->addDays(10),
        ]);

        $result = $this->service->renewLicense($license, 365);

        $this->assertTrue($result->expires_at->gt(now()->addDays(360)));
        $this->assertEquals(LicenseStatus::ACTIVE, $result->status);
    }

    /** @test */
    public function updates_license_attributes(): void
    {
        $licenseKey = LicenseKey::factory()->create(['brand_id' => $this->brand->id]);
        $license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $licenseKey->id,
            'product_name' => 'Old Name',
        ]);

        $result = $this->service->updateLicense($license, ['product_name' => 'New Name']);

        $this->assertEquals('New Name', $result->product_name);
    }

    /** @test */
    public function gets_license_by_id(): void
    {
        $licenseKey = LicenseKey::factory()->create(['brand_id' => $this->brand->id]);
        $license = License::factory()->create([
            'brand_id' => $this->brand->id,
            'license_key_id' => $licenseKey->id,
        ]);

        $result = $this->service->getLicenseById($license->id);

        $this->assertNotNull($result);
        $this->assertEquals($license->id, $result->id);
    }

    /** @test */
    public function returns_null_for_nonexistent_license(): void
    {
        $result = $this->service->getLicenseById('nonexistent-uuid');

        $this->assertNull($result);
    }
}

