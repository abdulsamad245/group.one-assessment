<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Key Authentication Middleware
 *
 * Validates API keys and scopes requests to the associated brand (tenant).
 *
 * The API key should be provided in the Authorization header as:
 * Authorization: Bearer {api_key}
 *
 * Or in the X-API-Key header:
 * X-API-Key: {api_key}
 */
class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$scopes  Optional scopes/permissions required
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        $apiKey = $this->extractApiKey($request);

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Provide it in X-API-Key header.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Extract prefix for faster lookup
        $prefix = ApiKey::extractPrefix($apiKey);

        if (!$prefix) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key format.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Find API key by prefix first (indexed), then verify hash
        $hashedKey = ApiKey::hash($apiKey);
        $apiKeyModel = ApiKey::where('prefix', $prefix)
            ->where('key', $hashedKey)
            ->with('brand')
            ->first();

        if (!$apiKeyModel) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if API key is valid (active and not expired)
        if (!$apiKeyModel->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'API key is inactive or expired.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the associated brand is active
        if (!$apiKeyModel->brand || !$apiKeyModel->brand->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Associated brand is inactive.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Check scopes/permissions if specified
        if (!empty($scopes) && !$this->hasRequiredScopes($apiKeyModel, $scopes)) {
            return response()->json([
                'success' => false,
                'message' => 'API key does not have the required permissions.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Update last used timestamp (async to avoid blocking)
        dispatch(function () use ($apiKeyModel) {
            $apiKeyModel->markAsUsed();
        })->afterResponse();

        // Attach brand and API key to request for use in controllers
        $request->merge([
            'authenticated_brand_id' => $apiKeyModel->brand_id,
            'authenticated_brand' => $apiKeyModel->brand,
            'api_key_id' => $apiKeyModel->id,
            'api_key_permissions' => $apiKeyModel->permissions ?? [],
        ]);

        // Set brand context for the request
        app()->instance('current_brand', $apiKeyModel->brand);

        return $next($request);
    }

    /**
     * Check if the API key has the required scopes/permissions.
     *
     * @param ApiKey $apiKey
     * @param array $requiredScopes
     * @return bool
     */
    private function hasRequiredScopes(ApiKey $apiKey, array $requiredScopes): bool
    {
        // If API key has no permissions set, it has full access
        if (empty($apiKey->permissions)) {
            return true;
        }

        // Check if API key has wildcard permission
        if (in_array('*', $apiKey->permissions)) {
            return true;
        }

        // Check if API key has all required scopes
        foreach ($requiredScopes as $scope) {
            if (!in_array($scope, $apiKey->permissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract API key from request headers.
     *
     * @param Request $request
     * @return string|null
     */
    private function extractApiKey(Request $request): ?string
    {
        // Try X-API-Key header
        $apiKeyHeader = $request->header('X-API-Key');
        if ($apiKeyHeader) {
            return $apiKeyHeader;
        }

        return null;
    }
}
