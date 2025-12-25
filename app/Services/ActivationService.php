<?php

namespace App\Services;

use App\DTOs\CreateActivationDTO;
use App\Enums\ActivationStatus;
use App\Enums\LicenseStatus;
use App\Events\LicenseActivated;
use App\Events\LicenseDeactivated;
use App\Models\Activation;
use App\Models\LicenseKey;
use App\Repositories\ActivationRepository;
use App\Repositories\LicenseKeyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivationService
{
    public function __construct(
        private readonly ActivationRepository $activationRepository,
        private readonly LicenseKeyRepository $licenseKeyRepository,
    ) {
    }

    /**
     * Activate a license key.
     */
    public function activate(CreateActivationDTO $dto): Activation
    {
        $licenseKey = $this->licenseKeyRepository->findByKey($dto->getLicenseKey());

        if (!$licenseKey) {
            throw new \Exception(__('messages.license-key-invalid'));
        }

        if (!$licenseKey->isValid()) {
            throw new \Exception(__('messages.license-key-not-valid'));
        }

        $license = $licenseKey->licenses()
            ->where('product_slug', $dto->getProductSlug())
            ->first();

        if (!$license) {
            throw new \Exception(__('messages.license-not-found-for-product'));
        }

        if (!$license->canActivate()) {
            throw new \Exception(__('messages.license-cannot-activate'));
        }

        $instanceType = $dto->getInstanceType();
        $instanceValue = $dto->getInstanceValue();
        $maxForType = $license->max_activations_per_instance[$instanceType] ?? null;

        if ($maxForType === null) {
            throw new \Exception(__('messages.instance-type-not-configured', [
                'instance_type' => $instanceType,
            ]));
        }

        $existingActivation = $this->activationRepository->findByInstanceTypeAndValue(
            $license->id,
            $instanceType,
            $instanceValue
        );

        if ($existingActivation) {
            $existingActivation->updateLastChecked();
            return $existingActivation;
        }

        $currentUniqueValues = $this->activationRepository->countUniqueActiveInstanceValues(
            $license->id,
            $instanceType
        );

        if ($currentUniqueValues >= $maxForType) {
            throw new \Exception(__('messages.license-max-activations-reached-for-instance-type', [
                'instance_type' => $instanceType,
                'max' => $maxForType,
            ]));
        }

        return DB::transaction(function () use ($licenseKey, $license, $dto, $instanceType, $instanceValue) {
            $activation = $this->activationRepository->create([
                'license_key_id' => $licenseKey->id,
                'license_id' => $license->id,
                'device_identifier' => $dto->getDeviceIdentifier(),
                'device_name' => $dto->getDeviceName(),
                'instance_type' => $instanceType,
                'instance_value' => $instanceValue,
                'ip_address' => $dto->getIpAddress(),
                'user_agent' => $dto->getUserAgent(),
                'status' => ActivationStatus::ACTIVE->value,
                'activated_at' => now(),
                'last_checked_at' => now(),
                'metadata' => $dto->getMetadata(),
            ]);

            $license->incrementActivations();

            LicenseActivated::dispatch(
                $license,
                $activation,
                "License activated for {$instanceType}: {$instanceValue}",
                [
                    'instance_type' => $instanceType,
                    'instance_value' => $instanceValue,
                    'ip_address' => $dto->getIpAddress(),
                ]
            );

            Log::info('License activated', [
                'license_id' => $license->id,
                'license_key_id' => $licenseKey->id,
                'activation_id' => $activation->id,
                'instance_type' => $instanceType,
                'instance_value' => $instanceValue,
            ]);

            return $activation;
        });
    }

    /**
     * Deactivate a license activation.
     */
    public function deactivate(Activation $activation): void
    {
        DB::transaction(function () use ($activation) {
            $license = $activation->license;

            $activation->deactivate();
            $license->decrementActivations();

            LicenseDeactivated::dispatch(
                $license,
                $activation,
                "License deactivated from {$activation->instance_type}: {$activation->instance_value}"
            );

            Log::info('License deactivated', [
                'license_id' => $license->id,
                'activation_id' => $activation->id,
                'instance_type' => $activation->instance_type,
                'instance_value' => $activation->instance_value,
            ]);
        });
    }

    /**
     * Get activation by ID.
     */
    public function getActivationById(string $id): ?Activation
    {
        return $this->activationRepository->findById($id);
    }

    /**
     * Check activation status for a license key.
     */
    public function checkStatus(string $licenseKey, string $productSlug): array
    {
        $key = $this->licenseKeyRepository->findByKey($licenseKey);

        if (!$key) {
            return [
                'valid' => false,
                'message' => 'Invalid license key',
            ];
        }

        $license = $key->licenses()->where('product_slug', $productSlug)->first();

        if (!$license) {
            return [
                'valid' => false,
                'message' => 'License not found for this product',
            ];
        }

        if ($license->isExpired()) {
            return [
                'valid' => false,
                'message' => 'License has expired',
                'expires_at' => $license->expires_at,
            ];
        }

        if ($license->status !== LicenseStatus::ACTIVE) {
            return [
                'valid' => false,
                'message' => 'License is not active',
                'status' => $license->status,
            ];
        }

        $entitlements = [];
        foreach ($license->max_activations_per_instance as $instanceType => $maxSeats) {
            $usedSeats = $this->activationRepository->countUniqueActiveInstanceValues(
                $license->id,
                $instanceType
            );
            $entitlements[$instanceType] = [
                'max_seats' => $maxSeats,
                'used_seats' => $usedSeats,
                'remaining_seats' => $maxSeats - $usedSeats,
            ];
        }

        return [
            'valid' => true,
            'license_type' => $license->license_type,
            'product_name' => $license->product_name,
            'product_slug' => $license->product_slug,
            'customer_name' => $license->customer_name,
            'entitlements' => $entitlements,
            'expires_at' => $license->expires_at,
        ];
    }
}
