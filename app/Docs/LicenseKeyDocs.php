<?php

namespace App\Docs;

/**
 * License Key API Documentation
 */
class LicenseKeyDocs
{
    /**
     * @OA\Get(
     *     path="/v1/license-keys/key/{key}",
     *     summary="Get a license key by key string",
     *     description="Retrieve full details of a license key by its key string (5 groups of 5 characters). Includes status, entitlements, associated licenses, and remaining activation slots per instance.",
     *     operationId="getLicenseKeyByKey",
     *     tags={"License Keys"},
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         required=true,
     *         description="The license key string (5 groups of 5 characters)",
     *         @OA\Schema(type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY")
     *     ),
     *     @OA\Parameter(
     *         name="X-Correlation-ID",
     *         in="header",
     *         description="Optional correlation ID for request tracing",
     *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License key retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License key found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="key", type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY"),
     *                 @OA\Property(property="customer_email", type="string", format="email"),
     *                 @OA\Property(property="status", type="string", enum={"active", "suspended", "revoked", "expired"}),
     *                 @OA\Property(property="activations_count", type="integer", example=2),
     *                 @OA\Property(
     *                     property="entitlements",
     *                     type="array",
     *                     description="Activation limits and usage per product and instance type",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="product_name", type="string", example="WP Rocket Pro"),
     *                         @OA\Property(property="product_slug", type="string", example="wp-rocket-pro"),
     *                         @OA\Property(property="product_sku", type="string", example="WPR-PRO-001"),
     *                         @OA\Property(property="license_type", type="string", example="subscription"),
     *                         @OA\Property(property="license_status", type="string", example="active"),
     *                         @OA\Property(property="license_expires_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(
     *                             property="instances",
     *                             type="object",
     *                             example={"site_url": {"max_activations": 3, "active_activations": 1, "remaining_seats": 2}}
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="licenses",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/License")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="License key not found"
     *     )
     * )
     */
    public function showByKey()
    {
    }
}
