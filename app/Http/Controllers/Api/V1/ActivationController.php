<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateLicenseRequest;
use App\Http\Requests\CheckActivationStatusRequest;
use App\Http\Requests\DeactivateLicenseRequest;
use App\Http\Resources\ActivationResource;
use App\Services\ActivationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Activation Controller
 *
 * Handles license activation operations.
 * API documentation is located in App\Docs\ActivationDocs.
 */
class ActivationController extends Controller
{
    public function __construct(
        private readonly ActivationService $activationService
    ) {
    }

    /**
     * Activate a license.
     */
    public function store(ActivateLicenseRequest $request): JsonResponse
    {
        try {
            $dto = $request->createActivationDTO();
            $activation = $this->activationService->activate($dto);
            return $this->created(__('messages.activation-created'), new ActivationResource($activation->load(['licenseKey', 'license'])));
        } catch (Exception $e) {
            Log::error('ActivationController.store (error)', ['message' => $e->getMessage(),]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Deactivate a license.
     */
    public function deactivate(DeactivateLicenseRequest $request): JsonResponse
    {
        try {
            $dto = $request->createDeactivationDTO();
            $activation = $this->activationService->getActivationById($dto->getActivationId());
            if (!$activation) {
                return $this->notFound(__('messages.activation-not-found'));
            }
            $this->activationService->deactivate($activation);
            return $this->response(Response::HTTP_OK, __('messages.activation-deactivated'), null);
        } catch (Exception $e) {
            Log::error('ActivationController.deactivate (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Check activation status.
     */
    public function status(CheckActivationStatusRequest $request): JsonResponse
    {
        try {
            $dto = $request->createCheckStatusDTO();
            $status = $this->activationService->checkStatus(
                $dto->getLicenseKey(),
                $dto->getProductSlug()
            );
            return $this->response(Response::HTTP_OK, __('messages.activation-status-checked'), $status);
        } catch (Exception $e) {
            Log::error('ActivationController.status (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
