<?php

namespace App\Repositories;

use App\Contracts\Repositories\LicenseRepositoryInterface;
use App\Models\License;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LicenseRepository implements LicenseRepositoryInterface
{
    /**
     * Get all licenses with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return License::with('brand')->paginate($perPage);
    }

    /**
     * Find license by ID.
     */
    public function findById(string $id): ?License
    {
        return License::with(['brand', 'licenseKey'])->find($id);
    }

    /**
     * Find licenses by customer email.
     */
    public function findByCustomerEmail(string $email): Collection
    {
        return License::with('brand')
            ->byCustomerEmail($email)
            ->get();
    }

    /**
     * Find licenses by customer email across all brands.
     */
    public function findByCustomerEmailAllBrands(string $email): Collection
    {
        return License::with('brand')
            ->where('customer_email', $email)
            ->get();
    }

    /**
     * Find licenses by brand.
     */
    public function findByBrand(int $brandId): Collection
    {
        return License::where('brand_id', $brandId)->get();
    }

    /**
     * Create a new license.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): License
    {
        return License::create($data);
    }

    /**
     * Update a license.
     *
     * @param array<string, mixed> $data
     */
    public function update(License $license, array $data): License
    {
        $license->update($data);
        return $license->fresh();
    }

    /**
     * Delete a license.
     */
    public function delete(License $license): bool
    {
        return $license->delete();
    }

    /**
     * Get expired licenses.
     */
    public function getExpired(): Collection
    {
        return License::where('expires_at', '<', now())
            ->where('status', '!=', 'expired')
            ->get();
    }

    /**
     * Get licenses expiring soon.
     */
    public function getExpiringSoon(int $days = 7): Collection
    {
        return License::whereBetween('expires_at', [now(), now()->addDays($days)])
            ->where('status', 'active')
            ->get();
    }
}
