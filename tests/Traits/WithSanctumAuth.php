<?php

/**
 * Tests Traits - WithSanctumAuth
 *
 * @package Tests\Traits
 */

namespace Tests\Traits;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

/**
 * WithSanctumAuth Trait
 * 
 * Provides helper methods for authenticating users with Sanctum in tests.
 */
trait WithSanctumAuth
{
    protected ?User $testUser = null;
    protected ?Brand $testBrand = null;

    /**
     * Create and authenticate a test user with Sanctum.
     */
    protected function actingAsUser(string $role = 'user', ?Brand $brand = null): User
    {
        if (!$brand) {
            $brand = $this->createTestBrand();
        }

        $user = User::create([
            'id' => (string) Str::uuid(),
            'brand_id' => $brand->id,
            'name' => 'Test User',
            'email' => 'test@mailinator.com',
            'password' => bcrypt('password'),
            'role' => $role,
        ]);

        Sanctum::actingAs($user);

        $this->testUser = $user;
        $this->testBrand = $brand;

        return $user;
    }

    /**
     * Create and authenticate an admin user.
     */
    protected function actingAsAdmin(?Brand $brand = null): User
    {
        return $this->actingAsUser('admin', $brand);
    }

    /**
     * Create and authenticate a super admin user.
     */
    protected function actingAsSuperAdmin(?Brand $brand = null): User
    {
        return $this->actingAsUser('super_admin', $brand);
    }

    /**
     * Create a test brand.
     */
    protected function createTestBrand(array $attributes = []): Brand
    {
        return Brand::create(array_merge([
            'id' => (string) Str::uuid(),
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test brand description',
            'contact_email' => 'contact@testbrand.com',
            'website' => 'https://testbrand.com',
            'is_active' => true,
        ], $attributes));
    }

    /**
     * Get the authenticated test user.
     */
    protected function getTestUser(): ?User
    {
        return $this->testUser;
    }

    /**
     * Get the test brand.
     */
    protected function getTestBrand(): ?Brand
    {
        return $this->testBrand;
    }
}

