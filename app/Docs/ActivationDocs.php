<?php

namespace App\Docs;

/**
 * Activation API Documentation
 */
class ActivationDocs
{
    /**
     * @OA\Post(
     *     path="/v1/activations",
     *     summary="Activate a license",
     *     description="Activate a license key for a specific instance type and value. Seat management is based on unique instance values per type.",
     *     operationId="activateLicense",
     *     tags={"Activations"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="X-Correlation-ID",
     *         in="header",
     *         description="Optional correlation ID for request tracing",
     *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"license_key", "product_slug", "instance_type", "instance_value"},
     *             @OA\Property(property="license_key", type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY", description="The license key to activate (5 groups of 5 characters)"),
     *             @OA\Property(property="product_slug", type="string", example="wp-rocket-pro", description="The product slug for this activation (machine-friendly identifier)"),
     *             @OA\Property(
     *                 property="instance_type",
     *                 type="string",
     *                 enum={"site_url", "host", "machine_id"},
     *                 example="site_url",
     *                 description="Type of instance identifier for seat management"
     *             ),
     *             @OA\Property(
     *                 property="instance_value",
     *                 type="string",
     *                 example="https://domain1.mailinator.com",
     *                 description="Value of the instance identifier (e.g., the actual URL, hostname, or machine ID)"
     *             ),
     *             @OA\Property(property="device_identifier", type="string", nullable=true, example="device-macbook-pro-001", description="Optional unique device identifier"),
     *             @OA\Property(property="device_name", type="string", nullable=true, example="John's MacBook Pro", description="Optional human-readable device name"),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 example={"os": "macOS", "version": "14.0", "app_version": "2.1.0"},
     *                 description="Additional device/activation metadata"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="License activated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License activated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Activation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Activation failed - Invalid key, expired, or max seats reached for instance type",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Maximum activations reached for instance type: site_url")
     *         )
     *     )
     * )
     */
    public function store()
    {
    }

    /**
     * @OA\Post(
     *     path="/v1/deactivations",
     *     summary="Deactivate a license",
     *     description="Deactivate an active license activation, freeing up an activation slot",
     *     operationId="deactivateLicense",
     *     tags={"Activations"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"activation_id"},
     *             @OA\Property(property="activation_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000", description="The activation ID (UUID) to deactivate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License deactivated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License deactivated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Activation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Activation not found")
     *         )
     *     )
     * )
     */
    public function deactivate()
    {
    }

    /**
     * @OA\Get(
     *     path="/v1/activations/status",
     *     summary="Check activation status",
     *     description="Check the activation status of a license key for a specific product. Returns seat usage per instance type.",
     *     operationId="checkActivationStatus",
     *     tags={"Activations"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="X-Correlation-ID",
     *         in="header",
     *         description="Optional correlation ID for request tracing",
     *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\Parameter(
     *         name="license_key",
     *         in="query",
     *         required=true,
     *         description="The license key to check (5 groups of 5 characters)",
     *         @OA\Schema(type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY")
     *     ),
     *     @OA\Parameter(
     *         name="product_slug",
     *         in="query",
     *         required=true,
     *         description="The product slug to check status for (machine-friendly identifier)",
     *         @OA\Schema(type="string", example="wp-rocket-pro")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activation status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="valid", type="boolean", example=true),
     *                 @OA\Property(property="license_type", type="string", example="standard"),
     *                 @OA\Property(property="product_name", type="string", example="WP Rocket Pro"),
     *                 @OA\Property(property="product_slug", type="string", example="wp-rocket-pro"),
     *                 @OA\Property(property="customer_name", type="string", example="John Doe"),
     *                 @OA\Property(
     *                     property="entitlements",
     *                     type="object",
     *                     description="Seat usage per instance type",
     *                     example={"site_url": {"max_seats": 3, "used_seats": 1, "remaining_seats": 2}, "host": {"max_seats": 5, "used_seats": 0, "remaining_seats": 5}}
     *                 ),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     )
     * )
     */
    public function status()
    {
    }
}
