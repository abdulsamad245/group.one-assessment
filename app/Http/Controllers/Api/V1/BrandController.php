<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Brand Controller
 *
 * Handles brand (tenant) management operations.
 * API documentation is located in App\Docs\BrandDocs.
 */
class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $brandService
    ) {
    }

    /**
     * List all brands.
     */
    public function index(): JsonResponse
    {
        try {
            $brands = $this->brandService->getAllBrands();
            return $this->response(Response::HTTP_OK, __('messages.brands-found'), BrandResource::collection($brands));
        } catch (Exception $e) {
            Log::error('BrandController.index (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Create a new brand.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        try {
            $dto = $request->createBrandDTO();
            $brand = $this->brandService->createBrand($dto);
            return $this->created(__('messages.brand-created'), new BrandResource($brand));
        } catch (Exception $e) {
            Log::error('BrandController.store (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Get a specific brand.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $brand = $this->brandService->getBrandById((int) $id);
            if (!$brand) {
                return $this->notFound(__('messages.brand-not-found'));
            }
            return $this->response(Response::HTTP_OK, __('messages.brand-found'), new BrandResource($brand));
        } catch (Exception $e) {
            Log::error('BrandController.show (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Update a brand.
     */
    public function update(UpdateBrandRequest $request, string $id): JsonResponse
    {
        try {
            $brand = $this->brandService->getBrandById((int) $id);
            if (!$brand) {
                return $this->notFound(__('messages.brand-not-found'));
            }
            $dto = $request->createUpdateBrandDTO();
            $updated = $this->brandService->updateBrand($brand, $dto->toArray());
            return $this->response(Response::HTTP_OK, __('messages.brand-updated'), new BrandResource($updated));
        } catch (Exception $e) {
            Log::error('BrandController.update (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Delete a brand.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $brand = $this->brandService->getBrandById((int) $id);
            if (!$brand) {
                return $this->notFound(__('messages.brand-not-found'));
            }
            $this->brandService->deleteBrand($brand);
            return $this->response(Response::HTTP_OK, __('messages.brand-deleted'), null);
        } catch (Exception $e) {
            Log::error('BrandController.destroy (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
