<?php

namespace App\Docs;

/**
 * License API Documentation
 */
class LicenseDocs
{
    // /**
    //  * @OA\Get(
    //  *     path="/v1/licenses",
    //  *     summary="List all licenses",
    //  *     description="Retrieve a paginated list of licenses for the authenticated brand",
    //  *     operationId="getLicenses",
    //  *     tags={"Licenses"},
    //  *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
    //  *     @OA\Parameter(
    //  *         name="page",
    //  *         in="query",
    //  *         description="Page number for pagination",
    //  *         @OA\Schema(type="integer", example=1)
    //  *     ),
    //  *     @OA\Parameter(
    //  *         name="per_page",
    //  *         in="query",
    //  *         description="Items per page",
    //  *         @OA\Schema(type="integer", example=15)
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Successful operation",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="string"),
    //  *             @OA\Property(
    //  *                 property="data",
    //  *                 type="object",
    //  *                 @OA\Property(property="current_page", type="integer"),
    //  *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/License")),
    //  *                 @OA\Property(property="total", type="integer")
    //  *             )
    //  *         )
    //  *     )
    //  * )
    //  */
    // public function index() {}

    /**
     * @OA\Post(
     *     path="/v1/licenses",
     *     summary="Create a new license",
     *     description="Create a new license for a customer. If no license key exists for the customer email, one will be generated.",
     *     operationId="createLicense",
     *     tags={"Licenses"},
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
     *             required={"customer_email", "product_name", "product_slug", "license_type"},
     *             @OA\Property(property="customer_email", type="string", format="email", example="customer@example.com"),
     *             @OA\Property(property="customer_name", type="string", example="John Doe"),
     *             @OA\Property(property="product_name", type="string", example="WP Rocket Pro", description="Human-readable product name"),
     *             @OA\Property(property="product_slug", type="string", example="wp-rocket-pro", description="Machine-friendly product identifier (lowercase, hyphen-separated)"),
     *             @OA\Property(property="license_type", type="string", enum={"perpetual", "subscription", "trial"}, example="subscription"),
     *             @OA\Property(
     *                 property="max_activations_per_instance",
     *                 type="object",
     *                 description="Maximum seats (unique values) per instance type for seat management. Keys are instance types (site_url, host, machine_id), values are the max number of unique values allowed.",
     *                 example={"site_url": 3, "host": 5, "machine_id": 1}
     *             ),
     *             @OA\Property(property="expires_at", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="metadata", type="object", example={"plan": "pro"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="License created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="license", ref="#/components/schemas/License"),
     *                 @OA\Property(property="license_key", type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY", description="Only returned when a new license key is generated")
     *             )
     *         )
     *     )
     * )
     */
    public function store()
    {
    }

    // /**
    //  * @OA\Get(
    //  *     path="/v1/licenses/{id}",
    //  *     summary="Get a specific license",
    //  *     description="Retrieve details of a specific license",
    //  *     operationId="getLicense",
    //  *     tags={"Licenses"},
    //  *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="License ID (UUID)",
    //  *         @OA\Schema(type="string", format="uuid")
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Successful operation",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="success", type="boolean", example=true),
    //  *             @OA\Property(property="data", ref="#/components/schemas/License")
    //  *         )
    //  *     ),
    //  *     @OA\Response(response=404, description="License not found")
    //  * )
    //  */
    // public function show() {}

    // /**
    //  * @OA\Put(
    //  *     path="/v1/licenses/{id}",
    //  *     summary="Update a license",
    //  *     description="Update license details such as expiration date or activation limits",
    //  *     operationId="updateLicense",
    //  *     tags={"Licenses"},
    //  *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="License ID (UUID)",
    //  *         @OA\Schema(type="string", format="uuid")
    //  *     ),
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="max_activations", type="integer"),
    //  *             @OA\Property(property="expires_at", type="string", format="date"),
    //  *             @OA\Property(property="is_active", type="boolean"),
    //  *             @OA\Property(property="metadata", type="object")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="License updated successfully"
    //  *     ),
    //  *     @OA\Response(response=404, description="License not found")
    //  * )
    //  */
    // public function update() {}

    /**
     * @OA\Post(
     *     path="/v1/licenses/{id}/renew",
     *     summary="Renew a license",
     *     description="Extend the expiration date of a license. Only applicable to subscription and trial licenses.",
     *     operationId="renewLicense",
     *     tags={"Licenses"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="License ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expires_at"},
     *             @OA\Property(property="expires_at", type="string", format="date", example="2026-12-31", description="New expiration date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License renewed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License renewed successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/License")
     *         )
     *     ),
     *     @OA\Response(response=404, description="License not found"),
     *     @OA\Response(response=422, description="License cannot be renewed (e.g., perpetual license)")
     * )
     */
    public function renew()
    {
    }

    /**
     * @OA\Post(
     *     path="/v1/licenses/{id}/suspend",
     *     summary="Suspend a license",
     *     description="Temporarily suspend a license. Suspended licenses cannot be used for activations.",
     *     operationId="suspendLicense",
     *     tags={"Licenses"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="License ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Payment overdue", description="Optional reason for suspension")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License suspended successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License suspended successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/License")
     *         )
     *     ),
     *     @OA\Response(response=404, description="License not found"),
     *     @OA\Response(response=422, description="License is already suspended or cancelled")
     * )
     */
    public function suspend()
    {
    }

    /**
     * @OA\Post(
     *     path="/v1/licenses/{id}/resume",
     *     summary="Resume a suspended license",
     *     description="Reactivate a previously suspended license.",
     *     operationId="resumeLicense",
     *     tags={"Licenses"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="License ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License resumed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License resumed successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/License")
     *         )
     *     ),
     *     @OA\Response(response=404, description="License not found"),
     *     @OA\Response(response=422, description="License is not suspended")
     * )
     */
    public function resume()
    {
    }

    /**
     * @OA\Post(
     *     path="/v1/licenses/{id}/cancel",
     *     summary="Cancel a license",
     *     description="Permanently cancel a license. This action cannot be undone. All active activations will be deactivated.",
     *     operationId="cancelLicense",
     *     tags={"Licenses"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="License ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Customer requested cancellation", description="Optional reason for cancellation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="License cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="License cancelled successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/License")
     *         )
     *     ),
     *     @OA\Response(response=404, description="License not found"),
     *     @OA\Response(response=422, description="License is already cancelled")
     * )
     */
    public function cancel()
    {
    }
}
