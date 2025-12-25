<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RenewLicenseRequest;
use App\Http\Requests\StoreLicenseRequest;
use App\Http\Requests\UpdateLicenseRequest;
use App\Http\Resources\LicenseResource;
use App\Services\LicenseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * License Controller
 *
 * Handles license management operations.
 * API documentation is located in App\Docs\LicenseDocs.
 */
class LicenseController extends Controller
{
    public function __construct(
        private readonly LicenseService $licenseService
    ) {
    }

    /**
     * List all licenses.
     */
    public function index(): JsonResponse
    {
        try {
            $licenses = $this->licenseService->getPaginatedLicenses();
            return $this->paginatedResponse($licenses, __('messages.licenses-found'));
        } catch (Exception $e) {
            Log::error('LicenseController.index (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Create a new license.
     */
    public function store(StoreLicenseRequest $request): JsonResponse
    {
        try {
            $dto = $request->createLicenseDTO();
            $result = $this->licenseService->createLicense($dto);

            return $this->created(__('messages.license-created'), $result);
        } catch (Exception $e) {
            Log::error('LicenseController.store (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Get a specific license.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            return $this->response(Response::HTTP_OK, __('messages.license-found'), new LicenseResource($license));
        } catch (Exception $e) {
            Log::error('LicenseController.show (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Update a license.
     */
    public function update(UpdateLicenseRequest $request, string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            $dto = $request->updateLicenseDTO();
            $updated = $this->licenseService->updateLicense($license, $dto->toArray());
            return $this->response(Response::HTTP_OK, __('messages.license-updated'), new LicenseResource($updated->load('brand')));
        } catch (Exception $e) {
            Log::error('LicenseController.update (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Renew a license.
     */
    public function renew(RenewLicenseRequest $request, string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            $days = $request->getDays();
            $renewed = $this->licenseService->renewLicense($license, $days);
            return $this->response(Response::HTTP_OK, __('messages.license-renewed'), new LicenseResource($renewed->load('brand')));
        } catch (Exception $e) {
            Log::error('LicenseController.renew (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Suspend a license.
     */
    public function suspend(string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            $suspended = $this->licenseService->suspendLicense($license);
            return $this->response(Response::HTTP_OK, __('messages.license-suspended'), new LicenseResource($suspended->load('brand')));
        } catch (Exception $e) {
            Log::error('LicenseController.suspend (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Resume a license.
     */
    public function resume(string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            $resumed = $this->licenseService->reactivateLicense($license);
            return $this->response(Response::HTTP_OK, __('messages.license-resumed'), new LicenseResource($resumed->load('brand')));
        } catch (Exception $e) {
            Log::error('LicenseController.resume (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Cancel a license.
     */
    public function cancel(string $id): JsonResponse
    {
        try {
            $license = $this->licenseService->getLicenseById($id);
            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }
            $canceled = $this->licenseService->updateLicense($license, ['status' => 'revoked']);
            return $this->response(Response::HTTP_OK, __('messages.license-canceled'), new LicenseResource($canceled->load('brand')));
        } catch (Exception $e) {
            Log::error('LicenseController.cancel (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
