<?php

namespace App\Services;

use App\Http\Resources\BrandResource;
use App\Http\Resources\LicenseKeyResource;
use App\Repositories\LicenseKeyRepository;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    public function __construct(
        private readonly LicenseKeyRepository $licenseKeyRepository,
    ) {
    }

    /**
     * Get all license keys for a customer by email.
     * Each license key contains all associated licenses.
     */
    public function getLicenseKeysByEmail(string $email): Collection
    {
        return $this->licenseKeyRepository->findByCustomerEmail($email);
    }

    /**
     * Get customer summary with formatted response ready for API.
     * Includes license keys, all associated licenses, and entitlements.
     *
     * @return array<string, mixed>
     */
    public function getCustomerSummary(string $email): array
    {
        $licenseKeys = $this->getLicenseKeysByEmail($email);

        $licenseKeys->load(['licenses', 'activations', 'brand']);

        $totalLicenses = $licenseKeys->sum(fn($key) => $key->licenses->count());
        $activeLicenses = $licenseKeys->sum(fn($key) => $key->licenses->where('status', 'active')->count());
        $totalActivations = $licenseKeys->sum(fn($key) => $key->activations->where('status', 'active')->count());

        $brands = $licenseKeys->pluck('brand')->unique('id')->values();

        return [
            'customer_email' => $email,
            'total_licenses' => $totalLicenses,
            'active_licenses' => $activeLicenses,
            'total_activations' => $totalActivations,
            'brands' => BrandResource::collection($brands),
            'license_key' => LicenseKeyResource::collection($licenseKeys),
        ];
    }
}
