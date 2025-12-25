<?php

use App\Http\Controllers\Api\V1\ActivationController;
use App\Http\Controllers\Api\V1\ApiKeyController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\LicenseController;
use App\Http\Controllers\Api\V1\LicenseKeyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Public License Key routes
    Route::get('license-keys/key/{key}', [LicenseKeyController::class, 'showByKey']);

    // Public Activation routes
    Route::post('activations', [ActivationController::class, 'store']);
    Route::post('deactivations', [ActivationController::class, 'deactivate']);
    Route::get('activations/status', [ActivationController::class, 'status']);

    // Routes requiring Sanctum authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        // Route::get('auth/me', [AuthController::class, 'me']);

        // Brand routes
        // Route::apiResource('brands', BrandController::class);

        // API Key management routes
        // Route::get('api-keys', [ApiKeyController::class, 'index']);
        Route::post('api-keys', [ApiKeyController::class, 'store']);
        // Route::post('api-keys/{id}/rotate', [ApiKeyController::class, 'rotate']);
        // Route::delete('api-keys/{id}', [ApiKeyController::class, 'destroy']);
    });

    // Routes requiring API key authentication
    Route::middleware(['api_key'])->group(function () {
    // Protected License Key routes
    // Route::apiResource('license-keys', LicenseKeyController::class)->only(['index', 'show']);

        // License routes
        Route::apiResource('licenses', LicenseController::class)->only(['index', 'store', 'show', 'update']);
        Route::post('licenses/{id}/renew', [LicenseController::class, 'renew']);
        Route::post('licenses/{id}/suspend', [LicenseController::class, 'suspend']);
        Route::post('licenses/{id}/resume', [LicenseController::class, 'resume']);
        Route::post('licenses/{id}/cancel', [LicenseController::class, 'cancel']);

        // Customer routes
        Route::get('customers/licenses', [CustomerController::class, 'licenses']);
    });
});
