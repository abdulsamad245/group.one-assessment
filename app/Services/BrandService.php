<?php

namespace App\Services;

use App\DTOs\BrandDTO;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Brand Service
 *
 * Handles all business logic for brand management.
 * All brand operations should go through this service.
 */
class BrandService
{
    public function __construct(
        private readonly BrandRepository $brandRepository
    ) {
    }

    /**
     * Get all brands.
     */
    public function getAllBrands(): Collection
    {
        return $this->brandRepository->all();
    }

    /**
     * Get a brand by ID.
     */
    public function getBrandById(int $id): ?Brand
    {
        return $this->brandRepository->findById($id);
    }

    /**
     * Get a brand by slug.
     */
    public function getBrandBySlug(string $slug): ?Brand
    {
        return $this->brandRepository->findBySlug($slug);
    }

    /**
     * Create a new brand.
     */
    public function createBrand(BrandDTO $dto): Brand
    {
        return $this->brandRepository->create($dto->toArray());
    }

    /**
     * Update an existing brand.
     */
    public function updateBrand(Brand $brand, array $data): Brand
    {
        return $this->brandRepository->update($brand, $data);
    }

    /**
     * Delete a brand.
     */
    public function deleteBrand(Brand $brand): bool
    {
        return $this->brandRepository->delete($brand);
    }

    /**
     * Get active brands only.
     */
    public function getActiveBrands(): Collection
    {
        return $this->brandRepository->getActive();
    }

    /**
     * Activate a brand.
     */
    public function activateBrand(Brand $brand): Brand
    {
        return $this->brandRepository->update($brand, ['is_active' => true]);
    }

    /**
     * Deactivate a brand.
     */
    public function deactivateBrand(Brand $brand): Brand
    {
        return $this->brandRepository->update($brand, ['is_active' => false]);
    }
}
