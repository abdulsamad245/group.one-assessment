<?php

namespace App\Docs;

/**
 * API Key Management Documentation
 */
class ApiKeyDocs
{
    // /**
    //  * @OA\Get(
    //  *     path="/v1/api-keys",
    //  *     summary="List all API keys",
    //  *     description="Retrieve all API keys for the authenticated user's brand. Note: The actual key value is not returned for security reasons.",
    //  *     operationId="listApiKeys",
    //  *     tags={"API Keys"},
    //  *     security={{"SanctumAuth": {}}},
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="API keys retrieved successfully",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="string", example="API keys retrieved successfully"),
    //  *             @OA\Property(
    //  *                 property="data",
    //  *                 type="array",
    //  *                 @OA\Items(ref="#/components/schemas/ApiKey")
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated"
    //  *     )
    //  * )
    //  */
    // public function index() {}

    /**
     * @OA\Post(
     *     path="/v1/api-keys",
     *     summary="Create a new API key",
     *     description="Create a new API key for the authenticated user's brand. The full key is only returned once upon creation.",
     *     operationId="createApiKey",
     *     tags={"API Keys"},
     *     security={{"SanctumAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Production API Key", description="A descriptive name for the API key"),
     *             @OA\Property(property="expires_at", type="string", format="date", example="2025-12-31", description="Optional expiration date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="API key created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API key created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="api_key", ref="#/components/schemas/ApiKey"),
     *                 @OA\Property(property="key", type="string", example="lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", description="The full API key - only shown once")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store()
    {
    }

    // /**
    //  * @OA\Post(
    //  *     path="/v1/api-keys/{id}/rotate",
    //  *     summary="Rotate an API key",
    //  *     description="Generate a new key value for an existing API key. The old key will immediately become invalid.",
    //  *     operationId="rotateApiKey",
    //  *     tags={"API Keys"},
    //  *     security={{"SanctumAuth": {}}},
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="API key ID (UUID)",
    //  *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="API key rotated successfully",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="string", example="API key rotated successfully"),
    //  *             @OA\Property(
    //  *                 property="data",
    //  *                 type="object",
    //  *                 @OA\Property(property="api_key", ref="#/components/schemas/ApiKey"),
    //  *                 @OA\Property(property="key", type="string", example="lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", description="The new API key - only shown once")
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="API key not found"
    //  *     )
    //  * )
    //  */
    // public function rotate() {}

    // /**
    //  * @OA\Delete(
    //  *     path="/v1/api-keys/{id}",
    //  *     summary="Revoke an API key",
    //  *     description="Permanently revoke an API key. This action cannot be undone.",
    //  *     operationId="revokeApiKey",
    //  *     tags={"API Keys"},
    //  *     security={{"SanctumAuth": {}}},
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="API key ID (UUID)",
    //  *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="API key revoked successfully",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="string", example="API key revoked successfully")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="API key not found"
    //  *     )
    //  * )
    //  */
    // public function destroy() {}
}
