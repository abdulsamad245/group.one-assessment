<?php

namespace App\Services;

use App\DTOs\CreateApiKeyDTO;
use App\Models\ApiKey;
use App\Repositories\ApiKeyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiKeyService
{
    public function __construct(
        private readonly ApiKeyRepository $apiKeyRepository
    ) {
    }

    /**
     * Get all API keys for a brand.
     */
    public function getApiKeysByBrand(string $brandId): array
    {
        return [
            'api_keys' => $this->apiKeyRepository->findByBrand($brandId)
        ];
    }

    /**
     * Get an API key by ID.
     */
    public function getApiKeyById(string $id): ?ApiKey
    {
        return $this->apiKeyRepository->findById($id);
    }

    /**
     * Create a new API key.
     */
    public function createApiKey(CreateApiKeyDTO $dto): array
    {
        return $this->generateApiKey(
            $dto->getBrandId(),
            $dto->getName(),
            $dto->getPermissions()
        );
    }

    /**
     * Generate an API key for a brand.
     */
    public function generateApiKey(string $brandId, string $name, ?array $permissions = null): array
    {
        return DB::transaction(function () use ($brandId, $name, $permissions) {
            // Generate random key parts
            $prefix = 'lcs_' . Str::random(8);
            $secret = Str::random(32);
            $plainKey = $prefix . '.' . $secret;

            // Hash the full key for storage
            $hashedKey = hash('sha256', $plainKey);

            // Create API key record
            $apiKey = $this->apiKeyRepository->create([
                'id' => Str::uuid()->toString(),
                'brand_id' => $brandId,
                'name' => $name,
                'key' => $hashedKey,
                'prefix' => $prefix,
                'permissions' => $permissions,
                'is_active' => true,
            ]);

            return [
                'api_key' => $apiKey,
                'plain_key' => $plainKey,
            ];
        });
    }

    /**
     * Rotate an API key.
     */
    public function rotateApiKey(string $id): array
    {
        return DB::transaction(function () use ($id) {
            $apiKey = $this->apiKeyRepository->findById($id);

            if (!$apiKey) {
                throw new \Exception(__('messages.api-key-not-found'));
            }

            // Generate new key
            $prefix = 'lcs_' . Str::random(8);
            $secret = Str::random(32);
            $plainKey = $prefix . '.' . $secret;
            $hashedKey = hash('sha256', $plainKey);

            // Update API key
            $apiKey = $this->apiKeyRepository->update($apiKey, [
                'key' => $hashedKey,
                'prefix' => $prefix,
            ]);

            return [
                'api_key' => $apiKey,
                'plain_key' => $plainKey,
            ];
        });
    }

    /**
     * Revoke an API key.
     */
    public function revokeApiKey(string $id): bool
    {
        $apiKey = $this->apiKeyRepository->findById($id);

        if (!$apiKey) {
            throw new \Exception(__('messages.api-key-not-found'));
        }

        return $this->apiKeyRepository->delete($apiKey);
    }

    /**
     * Verify an API key and return the associated brand.
     */
    public function verifyApiKey(string $plainKey): ?ApiKey
    {
        // Extract prefix from the key
        $parts = explode('.', $plainKey);
        if (count($parts) !== 2) {
            return null;
        }

        $prefix = $parts[0];
        $hashedKey = hash('sha256', $plainKey);

        // Find API key by prefix
        $apiKey = $this->apiKeyRepository->findByPrefix($prefix);

        if (!$apiKey || $apiKey->key !== $hashedKey || !$apiKey->is_active) {
            return null;
        }

        // Update last used timestamp
        $this->apiKeyRepository->updateLastUsed($apiKey);

        return $apiKey;
    }
}
