<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiKeyRequest;
use App\Services\ApiKeyService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiKeyController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ApiKeyService $apiKeyService
    ) {
    }

    /**
     * Get all API keys for the authenticated user's brand.
     */
    public function index(): JsonResponse
    {
        try {
            $brandId = auth()->user()->brand_id;
            $result = $this->apiKeyService->getApiKeysByBrand($brandId);

            return $this->response(Response::HTTP_OK, __('messages.api-keys-retrieved'), $result);
        } catch (\Exception $e) {
            Log::error('ApiKeyController.index (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Create a new API key.
     */
    public function store(StoreApiKeyRequest $request): JsonResponse
    {
        try {
            $dto = $request->createDTO();
            $result = $this->apiKeyService->createApiKey($dto);

            return $this->created(__('messages.api-key-created'), $result);
        } catch (\Exception $e) {
            Log::error('ApiKeyController.store (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Rotate an API key.
     */
    public function rotate(string $id): JsonResponse
    {
        try {
            $result = $this->apiKeyService->rotateApiKey($id);

            return $this->response(Response::HTTP_OK, __('messages.api-key-rotated'), $result);
        } catch (\Exception $e) {
            Log::error('ApiKeyController.rotate (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Revoke an API key.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->apiKeyService->revokeApiKey($id);

            return $this->response(Response::HTTP_OK, __('messages.api-key-revoked'));
        } catch (\Exception $e) {
            Log::error('ApiKeyController.destroy (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
