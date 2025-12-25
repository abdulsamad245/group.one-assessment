<?php

/**
 * Tests Traits - WithApiKey
 *
 * Provides helper methods for API key authentication in tests.
 *
 * @package Tests\Traits
 */

namespace Tests\Traits;

use App\Models\ApiKey;
use App\Models\Brand;

/**
 * Trait for tests that require API key authentication.
 *
 * Uses X-API-Key header for authentication.
 */
trait WithApiKey
{
    protected ?Brand $testBrand = null;
    protected ?string $testApiKey = null;

    /**
     * Set up API key authentication for tests.
     */
    protected function setUpApiKey(): void
    {
        $this->testBrand = Brand::factory()->create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        $plainKey = ApiKey::generate();
        $hashedKey = ApiKey::hash($plainKey);
        $prefix = ApiKey::extractPrefix($plainKey);

        ApiKey::create([
            'brand_id' => $this->testBrand->id,
            'name' => 'Test API Key',
            'key' => $hashedKey,
            'prefix' => $prefix,
            'is_active' => true,
        ]);

        $this->testApiKey = $plainKey;
    }

    /**
     * Make an authenticated GET request with X-API-Key header.
     */
    protected function getJsonWithApiKey(
        string $uri,
        array $headers = []
    ): \Illuminate\Testing\TestResponse {
        return $this->withHeaders(array_merge($headers, [
            'X-API-Key' => $this->testApiKey,
        ]))->getJson($uri);
    }

    /**
     * Make an authenticated POST request with X-API-Key header.
     */
    protected function postJsonWithApiKey(
        string $uri,
        array $data = [],
        array $headers = []
    ): \Illuminate\Testing\TestResponse {
        return $this->withHeaders(array_merge($headers, [
            'X-API-Key' => $this->testApiKey,
        ]))->postJson($uri, $data);
    }

    /**
     * Make an authenticated PUT request with X-API-Key header.
     */
    protected function putJsonWithApiKey(
        string $uri,
        array $data = [],
        array $headers = []
    ): \Illuminate\Testing\TestResponse {
        return $this->withHeaders(array_merge($headers, [
            'X-API-Key' => $this->testApiKey,
        ]))->putJson($uri, $data);
    }

    /**
     * Make an authenticated PATCH request with X-API-Key header.
     */
    protected function patchJsonWithApiKey(
        string $uri,
        array $data = [],
        array $headers = []
    ): \Illuminate\Testing\TestResponse {
        return $this->withHeaders(array_merge($headers, [
            'X-API-Key' => $this->testApiKey,
        ]))->patchJson($uri, $data);
    }

    /**
     * Make an authenticated DELETE request with X-API-Key header.
     */
    protected function deleteJsonWithApiKey(
        string $uri,
        array $data = [],
        array $headers = []
    ): \Illuminate\Testing\TestResponse {
        return $this->withHeaders(array_merge($headers, [
            'X-API-Key' => $this->testApiKey,
        ]))->deleteJson($uri, $data);
    }
}

