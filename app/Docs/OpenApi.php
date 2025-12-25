<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="group.one Centralized License Service API",
 *     description="Multi-tenant, multi-brand license management system API.

## Authentication Types

### 1. Sanctum Authentication (User Account Management)
Used for user account management and API key management. Obtain a Bearer token by logging in via `/v1/auth/login`.

### 2. API Key Authentication (License Operations)
Used for all license, activation, and brand management operations. API keys are managed via the account management endpoints.

## License Key Format
License keys are formatted as 5 groups of 5 characters: `XXXXX-XXXXX-XXXXX-XXXXX-XXXXX`

## Instance Identifiers
Activations are tied to specific instances. Instance identifiers can be:
- **Site URL**: `https://mysite.mailinator.com`
- **Host**: `host:server01.mailinator.com`
- **Machine ID**: `machine:ABC123-DEF456`

## Request Tracing
All API requests support the `X-Correlation-ID` header for request tracing. If not provided, one will be generated and returned in the response.",
 *     @OA\Contact(
 *         email="support@licenseservice.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="SanctumAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum Token",
 *     description="Laravel Sanctum Bearer token obtained from /v1/auth/login"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Key",
 *     description="API key in the format: lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyHeader",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-Key",
 *     description="API key for authentication via header"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints - Register, login, logout, and get current user"
 * )
 *
 * @OA\Tag(
 *     name="API Keys",
 *     description="API key management endpoints - Create, list, rotate, and revoke API keys"
 * )
 *
 * @OA\Tag(
 *     name="Brands",
 *     description="Brand management endpoints - Brands represent tenants in the system"
 * )
 *
 * @OA\Tag(
 *     name="Licenses",
 *     description="License management endpoints - Create and manage licenses for brands"
 * )
 *
 * @OA\Tag(
 *     name="License Keys",
 *     description="License key management endpoints - Generate and manage license keys (5 groups of 5 characters)"
 * )
 *
 * @OA\Tag(
 *     name="Activations",
 *     description="License activation endpoints - Activate, deactivate, and check activation status. Activations are tied to specific instances (site URL, host, or machine ID)."
 * )
 *
 * @OA\Tag(
 *     name="Customers",
 *     description="Customer lookup endpoints - Retrieve customer license information"
 * )
 */
class OpenApi
{
    // This class is used only for OpenAPI documentation annotations
}
