<?php

namespace App\Contracts\Repositories;

use App\Models\License;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LicenseRepositoryInterface
{
    /**
     * Get all licenses with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find license by ID.
     */
    public function findById(string $id): ?License;

    /**
     * Find licenses by customer email.
     */
    public function findByCustomerEmail(string $email): Collection;

    /**
     * Find licenses by customer email across all brands.
     */
    public function findByCustomerEmailAllBrands(string $email): Collection;

    /**
     * Find licenses by brand.
     */
    public function findByBrand(int $brandId): Collection;

    /**
     * Create a new license.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): License;

    /**
     * Update a license.
     *
     * @param array<string, mixed> $data
     */
    public function update(License $license, array $data): License;

    /**
     * Delete a license.
     */
    public function delete(License $license): bool;

    /**
     * Get expired licenses.
     */
    public function getExpired(): Collection;

    /**
     * Get licenses expiring soon.
     */
    public function getExpiringSoon(int $days = 7): Collection;
}
