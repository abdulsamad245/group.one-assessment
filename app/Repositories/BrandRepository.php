<?php

namespace App\Repositories;

use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * Get all brands.
     */
    public function all(): Collection
    {
        return Brand::all();
    }

    /**
     * Get active brands.
     */
    public function getActive(): Collection
    {
        return Brand::active()->get();
    }

    /**
     * Find brand by ID.
     */
    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    /**
     * Find brand by slug.
     */
    public function findBySlug(string $slug): ?Brand
    {
        return Brand::where('slug', $slug)->first();
    }

    /**
     * Create a new brand.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * Update a brand.
     *
     * @param array<string, mixed> $data
     */
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand->fresh();
    }

    /**
     * Delete a brand.
     */
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    /**
     * Check if brand exists by slug.
     */
    public function existsBySlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Brand::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
