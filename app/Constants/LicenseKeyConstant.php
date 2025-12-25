<?php

namespace App\Constants;

/**
 * License Key table column constants.
 */
class LicenseKeyConstant
{
    // Table name
    public const TABLE = 'license_keys';

    // Primary key
    public const ID = 'id';

    // Foreign keys
    public const BRAND_ID = 'brand_id';

    // Columns
    public const CUSTOMER_EMAIL = 'customer_email';
    public const KEY = 'key';
    public const KEY_HASH = 'key_hash';
    public const STATUS = 'status';
    public const EXPIRES_AT = 'expires_at';
    public const METADATA = 'metadata';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
