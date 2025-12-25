<?php

namespace App\Contracts\Repositories;

use App\Models\Activation;
use Illuminate\Database\Eloquent\Collection;

interface ActivationRepositoryInterface
{
    /**
     * Find activation by ID.
     */
    public function findById(string $id): ?Activation;

    /**
     * Get activations for a license key.
     */
    public function getByLicenseKey(string $licenseKeyId): Collection;

    /**
     * Get active activations for a license key.
     */
    public function getActiveByLicenseKey(string $licenseKeyId): Collection;

    /**
     * Find activation by device identifier.
     */
    public function findByDeviceIdentifier(string $licenseKeyId, string $deviceIdentifier): ?Activation;

    /**
     * Create a new activation.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Activation;

    /**
     * Update an activation.
     *
     * @param array<string, mixed> $data
     */
    public function update(Activation $activation, array $data): Activation;

    /**
     * Delete an activation.
     */
    public function delete(Activation $activation): bool;

    /**
     * Count active activations for a license key.
     */
    public function countActiveByLicenseKey(string $licenseKeyId): int;

    /**
     * Count unique active instance values for a license and instance type.
     */
    public function countUniqueActiveInstanceValues(string $licenseId, string $instanceType): int;

    /**
     * Find existing activation by license, instance type and value.
     */
    public function findByInstanceTypeAndValue(string $licenseId, string $instanceType, string $instanceValue): ?Activation;

    /**
     * Get stale activations (not checked recently).
     */
    public function getStaleActivations(int $days = 30): Collection;
}
