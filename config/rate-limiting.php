<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the rate limiting configuration for the group.one Centralized
    | License Service API. Rate limits are applied per API key to prevent
    | abuse and ensure fair usage across all brands.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Global API Rate Limit
    |--------------------------------------------------------------------------
    |
    | The default rate limit applied to all API endpoints unless overridden
    | by specific route middleware.
    |
    | Format: requests per minute
    |
    */

    'global' => [
        'requests' => env('RATE_LIMIT_GLOBAL', 60),
        'per_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoint-Specific Rate Limits
    |--------------------------------------------------------------------------
    |
    | Rate limits for specific endpoint groups. These override the global
    | rate limit for their respective routes.
    |
    */

    'endpoints' => [
        // Admin operations (brand management)
        'brands' => [
            'requests' => env('RATE_LIMIT_BRANDS', 60),
            'per_minutes' => 1,
        ],

        // Core license operations
        'licenses' => [
            'requests' => env('RATE_LIMIT_LICENSES', 120),
            'per_minutes' => 1,
        ],

        // License key operations
        'license_keys' => [
            'requests' => env('RATE_LIMIT_LICENSE_KEYS', 100),
            'per_minutes' => 1,
        ],

        // High-frequency activation operations
        'activations' => [
            'requests' => env('RATE_LIMIT_ACTIVATIONS', 200),
            'per_minutes' => 1,
        ],

        // Customer lookup operations
        'customers' => [
            'requests' => env('RATE_LIMIT_CUSTOMERS', 120),
            'per_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Headers
    |--------------------------------------------------------------------------
    |
    | Whether to include rate limit information in response headers.
    | When enabled, responses will include:
    | - X-RateLimit-Limit: Maximum requests allowed
    | - X-RateLimit-Remaining: Requests remaining in current window
    | - Retry-After: Seconds until rate limit resets (when exceeded)
    |
    */

    'headers' => env('RATE_LIMIT_HEADERS', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Storage
    |--------------------------------------------------------------------------
    |
    | The cache store to use for rate limiting. Defaults to Redis for
    | production environments, or the default cache driver otherwise.
    |
    */

    'store' => env('RATE_LIMIT_STORE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Response
    |--------------------------------------------------------------------------
    |
    | Customize the response when rate limit is exceeded.
    |
    */

    'response' => [
        'message' => 'Too many requests. Please try again later.',
        'status_code' => 429,
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Brand Rate Limits (Future Enhancement)
    |--------------------------------------------------------------------------
    |
    | Allow different rate limits per brand based on their subscription tier.
    | This is a future enhancement and not currently implemented.
    |
    */

    'per_brand' => [
        'enabled' => env('RATE_LIMIT_PER_BRAND', false),

        'tiers' => [
            'free' => [
                'requests' => 60,
                'per_minutes' => 1,
            ],
            'pro' => [
                'requests' => 300,
                'per_minutes' => 1,
            ],
            'enterprise' => [
                'requests' => 1000,
                'per_minutes' => 1,
            ],
        ],
    ],

];
