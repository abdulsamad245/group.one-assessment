<?php

namespace App\Docs;

/**
 * Customer API Documentation
 */
class CustomerDocs
{
    /**
     * @OA\Get(
     *     path="/v1/customers/licenses",
     *     summary="Get customer licenses",
     *     description="Retrieve all licenses and license keys for a customer by email address. Provides a complete summary of the customer's license entitlements.",
     *     operationId="getCustomerLicenses",
     *     tags={"Customers"},
     *     security={{"ApiKeyAuth": {}}, {"ApiKeyHeader": {}}},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="Customer's email address",
     *         @OA\Schema(type="string", format="email", example="customer@mailinator.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer licenses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer licenses found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="email", type="string", format="email", example="customer@mailinator.com"),
     *                 @OA\Property(
     *                     property="license_keys",
     *                     type="array",
     *                     description="All license keys belonging to the customer",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", format="uuid"),
     *                         @OA\Property(property="key", type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY"),
     *                         @OA\Property(property="status", type="string", enum={"active", "suspended", "revoked", "expired"}),
     *                         @OA\Property(
     *                             property="licenses",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="string", format="uuid"),
     *                                 @OA\Property(property="product_name", type="string", example="Premium Software"),
     *                                 @OA\Property(property="license_type", type="string", enum={"perpetual", "subscription", "trial"}),
     *                                 @OA\Property(property="status", type="string", enum={"active", "suspended", "cancelled", "expired"}),
     *                                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="activations",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Activation")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="total_licenses", type="integer", example=3),
     *                 @OA\Property(property="total_activations", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid email format"
     *     )
     * )
     */
    public function licenses()
    {
    }
}
