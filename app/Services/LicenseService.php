<?php

namespace App\Services;

use App\DTOs\CreateLicenseDTO;
use App\Enums\LicenseKeyStatus;
use App\Enums\LicenseStatus;
use App\Events\LicenseCreated;
use App\Events\LicenseKeyCreated;
use App\Events\LicenseKeyGenerated;
use App\Events\LicenseReactivated;
use App\Events\LicenseRenewed;
use App\Events\LicenseSuspended;
use App\Events\LicenseUpdated;
use App\Models\License;
use App\Repositories\LicenseRepository;
use App\Repositories\LicenseKeyRepository;
use App\Http\Resources\LicenseResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    public function __construct(
        private readonly LicenseRepository $licenseRepository,
        private readonly LicenseKeyRepository $licenseKeyRepository,
    ) {
    }

    /**
     * Get paginated licenses.
     */
    public function getPaginatedLicenses(int $perPage = 15)
    {
        return $this->licenseRepository->paginate($perPage);
    }

    /**
     * Get a license by ID.
     */
    public function getLicenseById(string $id): ?License
    {
        return $this->licenseRepository->findById($id);
    }

    /**
     * Create a new license.
     */
    public function createLicense(CreateLicenseDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $existingLicenseKey = $this->licenseKeyRepository->findByBrandAndCustomer(
                $dto->getCustomerEmail()
            );

            if ($existingLicenseKey) {
                $licenseKeyId = $existingLicenseKey->id;
                $plainKey = null;
            } else {
                $licenseKeyData = $this->generateLicenseKeyForCustomer(
                    $dto->getBrandId(),
                    $dto->getCustomerEmail()
                );
                $licenseKeyId = $licenseKeyData['license_key']->id;
                $plainKey = $licenseKeyData['plain_key'];
            }

            $licenseData = $dto->toArray();
            $licenseData['license_key_id'] = $licenseKeyId;
            $license = $this->licenseRepository->create($licenseData);

            LicenseCreated::dispatch(
                $license,
                'License created for customer: ' . $dto->getCustomerEmail()
            );

            Log::info('License created', [
                'license_id' => $license->id,
                'customer_email' => $dto->getCustomerEmail(),
                'brand_id' => $dto->getBrandId(),
                'license_key_id' => $licenseKeyId,
                'new_key_generated' => $plainKey !== null,
            ]);

            return [
                'license' => new LicenseResource($license->load('brand')),
                'license_key' => $plainKey,
            ];
        });
    }

    /**
     * Update a license.
     *
     * @param array<string, mixed> $data
     */
    public function updateLicense(License $license, array $data): License
    {
        return DB::transaction(function () use ($license, $data) {
            $updated = $this->licenseRepository->update($license, $data);

            LicenseUpdated::dispatch(
                $updated,
                'License updated'
            );

            Log::info('License updated', [
                'license_id' => $updated->id,
            ]);

            return $updated;
        });
    }

    /**
     * Generate a license key for a license.
     */
    public function generateLicenseKey(License $license): string
    {
        $key = $this->generateUniqueKey();

        $this->licenseKeyRepository->create([
            'license_id' => $license->id,
            'key' => $key,
            'status' => LicenseKeyStatus::ACTIVE->value,
            'expires_at' => $license->expires_at,
        ]);

        LicenseKeyGenerated::dispatch(
            $license,
            'New license key generated'
        );

        Log::info('License key generated', [
            'license_id' => $license->id,
            'key' => $key,
        ]);

        return $key;
    }

    /**
     * Generate a license key for a customer.
     */
    private function generateLicenseKeyForCustomer(string $brandId, string $customerEmail): array
    {
        $key = $this->generateUniqueKey();

        $licenseKey = $this->licenseKeyRepository->create([
            'id' => Str::uuid()->toString(),
            'brand_id' => $brandId,
            'customer_email' => $customerEmail,
            'key' => $key,
            'key_hash' => hash('sha256', $key),
            'status' => LicenseKeyStatus::ACTIVE->value,
            'expires_at' => null,
        ]);

        LicenseKeyCreated::dispatch($licenseKey, 'License key created for customer');

        Log::info('License key generated', [
            'license_key_id' => $licenseKey->id,
            'brand_id' => $brandId,
            'customer_email' => $customerEmail,
        ]);

        return [
            'license_key' => $licenseKey,
            'plain_key' => $key,
        ];
    }

    /**
     * Generate a unique license key.
     * Format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX
     */
    private function generateUniqueKey(): string
    {
        do {
            $key = $this->formatLicenseKey(Str::random(25));
        } while ($this->licenseKeyRepository->keyExists($key));

        return $key;
    }

    /**
     * Format license key with dashes.
     */
    private function formatLicenseKey(string $key): string
    {
        $key = strtoupper($key);
        return implode('-', str_split($key, 5));
    }

    /**
     * Suspend a license.
     */
    public function suspendLicense(License $license): License
    {
        $updated = $this->updateLicense($license, ['status' => LicenseStatus::SUSPENDED->value]);

        // Dispatch license suspended event
        LicenseSuspended::dispatch(
            $updated,
            'License suspended'
        );

        return $updated;
    }

    /**
     * Reactivate a license.
     */
    public function reactivateLicense(License $license): License
    {
        $updated = $this->updateLicense($license, ['status' => LicenseStatus::ACTIVE->value]);

        LicenseReactivated::dispatch(
            $updated,
            'License reactivated'
        );

        return $updated;
    }

    /**
     * Renew a license.
     */
    public function renewLicense(License $license, int $days): License
    {
        $newExpiryDate = now()->addDays($days);

        $updated = $this->updateLicense($license, [
            'expires_at' => $newExpiryDate,
            'status' => LicenseStatus::ACTIVE->value,
        ]);

        LicenseRenewed::dispatch(
            $updated,
            "License renewed for {$days} days"
        );

        return $updated;
    }
}
