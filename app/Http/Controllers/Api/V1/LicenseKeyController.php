<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseKeyResource;
use App\Repositories\LicenseKeyRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * License Key Controller
 *
 * Handles license key management operations.
 */
class LicenseKeyController extends Controller
{
    public function __construct(
        private readonly LicenseKeyRepository $licenseKeyRepository
    ) {
    }

    /**
     * List all license keys for the authenticated brand.
     */
    public function index(): JsonResponse
    {
        try {
            $licenseKeys = $this->licenseKeyRepository->getActive();
            return $this->response(
                Response::HTTP_OK,
                __('messages.license-keys-found'),
                LicenseKeyResource::collection($licenseKeys)
            );
        } catch (Exception $e) {
            Log::error('LicenseKeyController.index (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Get a specific license key with full details.
     * Shows status, entitlements, licenses, and remaining seats.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $licenseKey = $this->licenseKeyRepository->findById($id);

            if (!$licenseKey) {
                return $this->notFound(__('messages.license-key-not-found'));
            }

            // Load all relationships for full details
            $licenseKey->load(['licenses', 'activations', 'brand']);

            return $this->response(
                Response::HTTP_OK,
                __('messages.license-key-found'),
                new LicenseKeyResource($licenseKey)
            );
        } catch (Exception $e) {
            Log::error('LicenseKeyController.show (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Get a license key by key string.
     * Shows status, entitlements, licenses, and remaining seats.
     */
    public function showByKey(string $key): JsonResponse
    {
        try {
            $licenseKey = $this->licenseKeyRepository->findByKey($key);

            if (!$licenseKey) {
                return $this->notFound(__('messages.license-key-not-found'));
            }

            // Load all relationships for full details
            $licenseKey->load(['licenses', 'activations', 'brand']);

            return $this->response(
                Response::HTTP_OK,
                __('messages.license-key-found'),
                new LicenseKeyResource($licenseKey)
            );
        } catch (Exception $e) {
            Log::error('LicenseKeyController.showByKey (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
