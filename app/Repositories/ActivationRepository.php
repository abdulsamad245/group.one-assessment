<?php

namespace App\Repositories;

use App\Contracts\Repositories\ActivationRepositoryInterface;
use App\Models\Activation;
use Illuminate\Database\Eloquent\Collection;

class ActivationRepository implements ActivationRepositoryInterface
{
    /**
     * Find activation by ID.
     */
    public function findById(string $id): ?Activation
    {
        return Activation::with(['licenseKey', 'license'])->find($id);
    }

    /**
     * Get activations for a license key.
     */
    public function getByLicenseKey(string $licenseKeyId): Collection
    {
        return Activation::where('license_key_id', $licenseKeyId)->get();
    }

    /**
     * Get active activations for a license key.
     */
    public function getActiveByLicenseKey(string $licenseKeyId): Collection
    {
        return Activation::where('license_key_id', $licenseKeyId)
            ->active()
            ->get();
    }

    /**
     * Find activation by device identifier.
     */
    public function findByDeviceIdentifier(string $licenseKeyId, string $deviceIdentifier): ?Activation
    {
        return Activation::where('license_key_id', $licenseKeyId)
            ->where('device_identifier', $deviceIdentifier)
            ->active()
            ->first();
    }

    /**
     * Create a new activation.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Activation
    {
        return Activation::create($data);
    }

    /**
     * Update an activation.
     *
     * @param array<string, mixed> $data
     */
    public function update(Activation $activation, array $data): Activation
    {
        $activation->update($data);
        return $activation->fresh();
    }

    /**
     * Delete an activation.
     */
    public function delete(Activation $activation): bool
    {
        return $activation->delete();
    }

    /**
     * Count active activations for a license key.
     */
    public function countActiveByLicenseKey(string $licenseKeyId): int
    {
        return Activation::where('license_key_id', $licenseKeyId)
            ->active()
            ->count();
    }

    /**
     * Count unique active instance values for a license and instance type.
     * This counts distinct instance_value entries for a given instance_type.
     */
    public function countUniqueActiveInstanceValues(string $licenseId, string $instanceType): int
    {
        return Activation::where('license_id', $licenseId)
            ->where('instance_type', $instanceType)
            ->active()
            ->distinct('instance_value')
            ->count('instance_value');
    }

    /**
     * Find existing activation by license, instance type and value.
     */
    public function findByInstanceTypeAndValue(string $licenseId, string $instanceType, string $instanceValue): ?Activation
    {
        return Activation::where('license_id', $licenseId)
            ->where('instance_type', $instanceType)
            ->where('instance_value', $instanceValue)
            ->active()
            ->first();
    }

    /**
     * Get stale activations (not checked recently).
     */
    public function getStaleActivations(int $days = 30): Collection
    {
        return Activation::active()
            ->where('last_checked_at', '<', now()->subDays($days))
            ->orWhereNull('last_checked_at')
            ->get();
    }
}
