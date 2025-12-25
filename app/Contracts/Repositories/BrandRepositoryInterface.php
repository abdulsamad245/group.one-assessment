<?php

namespace App\Contracts\Repositories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface
{
    /**
     * Get all brands.
     */
    public function all(): Collection;

    /**
     * Get active brands.
     */
    public function getActive(): Collection;

    /**
     * Find brand by ID.
     */
    public function findById(int $id): ?Brand;

    /**
     * Find brand by slug.
     */
    public function findBySlug(string $slug): ?Brand;

    /**
     * Create a new brand.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Brand;

    /**
     * Update a brand.
     *
     * @param array<string, mixed> $data
     */
    public function update(Brand $brand, array $data): Brand;

    /**
     * Delete a brand.
     */
    public function delete(Brand $brand): bool;

    /**
     * Check if brand exists by slug.
     */
    public function existsBySlug(string $slug, ?int $excludeId = null): bool;
}
