<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseKey;
use App\Repositories\LicenseKeyRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * License Key Service
 *
 * Handles all business logic for license key management.
 * All license key operations should go through this service.
 */
class LicenseKeyService
{
    public function __construct(
        private readonly LicenseKeyRepository $licenseKeyRepository,
        private readonly LicenseService $licenseService
    ) {
    }

    /**
     * Get all active license keys.
     */
    public function getActiveLicenseKeys(): Collection
    {
        return $this->licenseKeyRepository->getActive();
    }

    /**
     * Get a license key by ID.
     */
    public function getLicenseKeyById(string $id): ?LicenseKey
    {
        return $this->licenseKeyRepository->findById($id);
    }

    /**
     * Get a license key by key string.
     */
    public function getLicenseKeyByKey(string $key): ?LicenseKey
    {
        return $this->licenseKeyRepository->findByKey($key);
    }

    /**
     * Generate a new license key for a license.
     */
    public function generateLicenseKey(License $license): LicenseKey
    {
        // Use LicenseService to generate the key
        $keyString = $this->licenseService->generateLicenseKey($license);

        // Retrieve and return the created license key
        $licenseKey = $this->licenseKeyRepository->findByKey($keyString);

        if (!$licenseKey) {
            throw new \RuntimeException('Failed to generate license key');
        }

        return $licenseKey;
    }

    /**
     * Revoke a license key.
     */
    public function revokeLicenseKey(LicenseKey $licenseKey): bool
    {
        return $this->licenseKeyRepository->delete($licenseKey);
    }

    /**
     * Get license keys for a specific license.
     */
    public function getLicenseKeysByLicenseId(string $licenseId): Collection
    {
        return $this->licenseKeyRepository->getByLicense($licenseId);
    }

    /**
     * Check if a license key is valid and active.
     */
    public function isLicenseKeyValid(string $key): bool
    {
        $licenseKey = $this->getLicenseKeyByKey($key);

        if (!$licenseKey) {
            return false;
        }

        if ($licenseKey->trashed()) {
            return false;
        }

        if ($licenseKey->expires_at && $licenseKey->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
