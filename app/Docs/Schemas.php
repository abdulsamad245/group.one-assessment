<?php

namespace App\Docs;

/**
 * OpenAPI Schema Definitions
 */
class Schemas
{
    /**
     * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     title="User",
     *     description="User account model",
     *     required={"id", "name", "email", "brand_id"},
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="brand_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="john@mailinator.com"),
     *     @OA\Property(property="role", type="string", enum={"admin", "user"}, example="user"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(property="brand", ref="#/components/schemas/Brand", description="Associated brand (included when eager loaded)")
     * )
     */
    public function userSchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="ApiKey",
     *     type="object",
     *     title="API Key",
     *     description="API key for authentication",
     *     required={"id", "brand_id", "name"},
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="brand_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *     @OA\Property(property="name", type="string", example="Production API Key"),
     *     @OA\Property(property="prefix", type="string", example="lcs_xxxxxxxx", description="Key prefix for identification"),
     *     @OA\Property(property="last_used_at", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="is_active", type="boolean", example=true),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
    public function apiKeySchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="Brand",
     *     type="object",
     *     title="Brand",
     *     description="Brand (Tenant) model",
     *     required={"id", "name", "slug"},
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="name", type="string", example="Acme Corporation", description="Brand name"),
     *     @OA\Property(property="slug", type="string", example="acme-corp", description="URL-friendly identifier"),
     *     @OA\Property(property="description", type="string", nullable=true, example="Leading software company"),
     *     @OA\Property(property="contact_email", type="string", format="email", nullable=true, example="contact@acme.com"),
     *     @OA\Property(property="website", type="string", format="url", nullable=true, example="https://acme.com"),
     *     @OA\Property(property="settings", type="object", nullable=true, example={"theme": "dark"}),
     *     @OA\Property(property="is_active", type="boolean", example=true),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
     * )
     */
    public function brandSchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="License",
     *     type="object",
     *     title="License",
     *     description="License model",
     *     required={"id", "brand_id", "license_key_id", "product_name", "product_slug", "license_type"},
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="brand_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *     @OA\Property(property="license_key_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440002"),
     *     @OA\Property(property="product_name", type="string", example="WP Rocket Pro", description="Human-readable product name"),
     *     @OA\Property(property="product_slug", type="string", example="wp-rocket-pro", description="Machine-friendly product identifier"),
     *     @OA\Property(property="license_type", type="string", enum={"perpetual", "subscription", "trial"}, example="subscription"),
     *     @OA\Property(property="status", type="string", enum={"active", "suspended", "cancelled", "expired"}, example="active"),
     *     @OA\Property(
     *         property="max_activations_per_instance",
     *         type="object",
     *         description="Maximum seats (unique values) allowed per instance type for seat management",
     *         example={"site_url": 3, "host": 5, "machine_id": 1}
     *     ),
     *     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-12-31T23:59:59Z"),
     *     @OA\Property(property="metadata", type="object", nullable=true, example={"plan": "pro"}),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(
     *         property="brand",
     *         ref="#/components/schemas/Brand",
     *         description="Associated brand (included when eager loaded)"
     *     ),
     *     @OA\Property(
     *         property="license_key",
     *         ref="#/components/schemas/LicenseKey",
     *         description="Associated license key (included when eager loaded)"
     *     )
     * )
     */
    public function licenseSchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="LicenseKey",
     *     type="object",
     *     title="License Key",
     *     description="License key model - keys are formatted as 5 groups of 5 characters",
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="brand_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *     @OA\Property(property="customer_email", type="string", format="email", example="customer@mailinator.com"),
     *     @OA\Property(property="key", type="string", example="ABCDE-FGHIJ-KLMNO-PQRST-UVWXY", description="License key in 5 groups of 5 characters"),
     *     @OA\Property(property="status", type="string", enum={"active", "suspended", "revoked", "expired"}, example="active"),
     *     @OA\Property(
     *         property="max_activations_per_instance",
     *         type="object",
     *         description="Maximum seats (unique values) allowed per instance type for seat management",
     *         example={"site_url": 3, "host": 5, "machine_id": 1}
     *     ),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
    public function licenseKeySchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="Activation",
     *     type="object",
     *     title="Activation",
     *     description="License activation model - activations are tied to specific instance types and values for seat management",
     *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *     @OA\Property(property="license_key_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *     @OA\Property(property="license_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440002"),
     *     @OA\Property(property="device_identifier", type="string", nullable=true, example="device-macbook-pro-001", description="Optional unique device identifier"),
     *     @OA\Property(property="device_name", type="string", nullable=true, example="John's MacBook Pro"),
     *     @OA\Property(
     *         property="instance_type",
     *         type="string",
     *         enum={"site_url", "host", "machine_id"},
     *         example="site_url",
     *         description="Type of instance identifier for seat management"
     *     ),
     *     @OA\Property(
     *         property="instance_value",
     *         type="string",
     *         example="https://domain1.mailinator.com",
     *         description="Value of the instance identifier"
     *     ),
     *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *     @OA\Property(property="activated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *     @OA\Property(property="deactivated_at", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="last_checked_at", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="metadata", type="object", nullable=true, example={"os": "macOS", "version": "14.0"}),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(
     *         property="license_key",
     *         ref="#/components/schemas/LicenseKey",
     *         description="Associated license key (included when eager loaded)"
     *     ),
     *     @OA\Property(
     *         property="license",
     *         ref="#/components/schemas/License",
     *         description="Associated license (included when eager loaded)"
     *     )
     * )
     */
    public function activationSchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="Error",
     *     type="object",
     *     title="Error Response",
     *     description="Standard error response",
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="message", type="string", example="An error occurred"),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         nullable=true,
     *         description="Validation errors (if applicable)",
     *         example={"field": "Error message"}
     *     )
     * )
     */
    public function errorSchema()
    {
    }

    /**
     * @OA\Schema(
     *     schema="PaginatedResponse",
     *     type="object",
     *     title="Paginated Response",
     *     description="Standard paginated response wrapper",
     *     @OA\Property(property="success", type="boolean", example=true),
     *     @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *     @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *         @OA\Property(property="first_page_url", type="string"),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="last_page_url", type="string"),
     *         @OA\Property(property="next_page_url", type="string", nullable=true),
     *         @OA\Property(property="path", type="string"),
     *         @OA\Property(property="per_page", type="integer", example=15),
     *         @OA\Property(property="prev_page_url", type="string", nullable=true),
     *         @OA\Property(property="to", type="integer", example=15),
     *         @OA\Property(property="total", type="integer", example=100)
     *     )
     * )
     */
    public function paginatedResponseSchema()
    {
    }
}
