<?php

namespace App\Contracts\Repositories;

use App\Models\LicenseKey;
use Illuminate\Database\Eloquent\Collection;

interface LicenseKeyRepositoryInterface
{
    /**
     * Find license key by key string.
     */
    public function findByKey(string $key): ?LicenseKey;

    /**
     * Find license key by ID.
     */
    public function findById(string $id): ?LicenseKey;

    /**
     * Get license keys for a license.
     */
    public function getByLicense(string $licenseId): Collection;

    /**
     * Create a new license key.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): LicenseKey;

    /**
     * Update a license key.
     *
     * @param array<string, mixed> $data
     */
    public function update(LicenseKey $licenseKey, array $data): LicenseKey;

    /**
     * Delete a license key.
     */
    public function delete(LicenseKey $licenseKey): bool;

    /**
     * Check if key exists.
     */
    public function keyExists(string $key): bool;

    /**
     * Get active license keys.
     */
    public function getActive(): Collection;
}
