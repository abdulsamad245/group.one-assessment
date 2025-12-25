<?php

namespace App\Repositories;

use App\Models\ApiKey;
use Illuminate\Database\Eloquent\Collection;

class ApiKeyRepository
{
    /**
     * Find API key by ID.
     */
    public function findById(string $id): ?ApiKey
    {
        return ApiKey::with('brand')->find($id);
    }

    /**
     * Find API key by prefix.
     */
    public function findByPrefix(string $prefix): ?ApiKey
    {
        return ApiKey::where('prefix', $prefix)->first();
    }

    /**
     * Find active API keys by brand.
     */
    public function findActiveByBrand(string $brandId): Collection
    {
        return ApiKey::where('brand_id', $brandId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Find all API keys by brand (including inactive).
     */
    public function findByBrand(string $brandId): Collection
    {
        return ApiKey::where('brand_id', $brandId)->get();
    }

    /**
     * Create a new API key.
     */
    public function create(array $data): ApiKey
    {
        return ApiKey::create($data);
    }

    /**
     * Update an API key.
     */
    public function update(ApiKey $apiKey, array $data): ApiKey
    {
        $apiKey->update($data);
        return $apiKey->fresh();
    }

    /**
     * Delete an API key.
     */
    public function delete(ApiKey $apiKey): bool
    {
        return $apiKey->delete();
    }

    /**
     * Update last used timestamp.
     */
    public function updateLastUsed(ApiKey $apiKey): void
    {
        $apiKey->update(['last_used_at' => now()]);
    }
}
