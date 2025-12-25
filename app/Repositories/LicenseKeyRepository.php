<?php

namespace App\Repositories;

use App\Contracts\Repositories\LicenseKeyRepositoryInterface;
use App\Models\LicenseKey;
use Illuminate\Database\Eloquent\Collection;

class LicenseKeyRepository implements LicenseKeyRepositoryInterface
{
    /**
     * Find license key by key string.
     */
    public function findByKey(string $key): ?LicenseKey
    {
        $keyHash = hash('sha256', $key);

        return LicenseKey::with(['licenses.brand', 'activations'])
            ->where('key_hash', $keyHash)
            ->first();
    }

    /**
     * Find license key by ID.
     */
    public function findById(string $id): ?LicenseKey
    {
        return LicenseKey::with(['licenses', 'activations', 'brand'])->find($id);
    }

    /**
     * Find license key by brand and customer email.
     */
    public function findByBrandAndCustomer(string $customerEmail): ?LicenseKey
    {
        return LicenseKey::where('customer_email', $customerEmail)
            ->first();
    }

    /**
     * Find all license keys by customer email (scoped by brand via global scope).
     */
    public function findByCustomerEmail(string $customerEmail): Collection
    {
        return LicenseKey::where('customer_email', $customerEmail)->get();
    }

    /**
     * Get license keys for a license.
     */
    public function getByLicense(string $licenseId): Collection
    {
        return LicenseKey::where('license_id', $licenseId)->get();
    }

    /**
     * Create a new license key.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): LicenseKey
    {
        return LicenseKey::create($data);
    }

    /**
     * Update a license key.
     *
     * @param array<string, mixed> $data
     */
    public function update(LicenseKey $licenseKey, array $data): LicenseKey
    {
        $licenseKey->update($data);
        return $licenseKey->fresh();
    }

    /**
     * Delete a license key.
     */
    public function delete(LicenseKey $licenseKey): bool
    {
        return $licenseKey->delete();
    }

    /**
     * Check if key exists.
     */
    public function keyExists(string $key): bool
    {
        return LicenseKey::where('key', $key)->exists();
    }

    /**
     * Get active license keys.
     */
    public function getActive(): Collection
    {
        return LicenseKey::active()->get();
    }
}
