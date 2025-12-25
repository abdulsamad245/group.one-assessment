<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = $request->createDTO();
            $result = $this->authService->register($dto);

            return $this->created(__('messages.user-registered'), $result);
        } catch (\Exception $e) {
            Log::error('AuthController.register (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Login a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = $request->createDTO();
            $result = $this->authService->login($dto);

            return $this->response(Response::HTTP_OK, __('messages.user-logged-in'), $result);
        } catch (\Exception $e) {
            Log::error('AuthController.login (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }

    /**
     * Logout a user.
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout(auth()->user());

            return $this->response(Response::HTTP_OK, __('messages.user-logged-out'));
        } catch (\Exception $e) {
            Log::error('AuthController.logout (error)', ['message' => $e->getMessage(), ]);
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }

    /**
     * Get authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->response(
            Response::HTTP_OK,
            __('messages.user-retrieved'),
            ['user' => auth()->user()->load('brand')]
        );
    }
}
