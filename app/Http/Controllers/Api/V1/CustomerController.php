<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCustomerLicensesRequest;
use App\Services\CustomerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer Controller
 *
 * Handles customer lookup operations.
 */
class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    /**
     * Get all licenses for a customer by email.
     */
    public function licenses(GetCustomerLicensesRequest $request): JsonResponse
    {
        try {
            $dto = $request->createCustomerLicensesDTO();
            $summary = $this->customerService->getCustomerSummary($dto->getEmail());
            return $this->response(Response::HTTP_OK, __('messages.customer-licenses-found'), $summary);
        } catch (Exception $e) {
            Log::error('CustomerController.licenses (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
