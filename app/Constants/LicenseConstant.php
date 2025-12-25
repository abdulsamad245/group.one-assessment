<?php

namespace App\Constants;

/**
 * License table column constants.
 */
class LicenseConstant
{
    // Table name
    public const TABLE = 'licenses';

    // Primary key
    public const ID = 'id';

    // Foreign keys
    public const LICENSE_KEY_ID = 'license_key_id';
    public const BRAND_ID = 'brand_id';

    // Columns
    public const CUSTOMER_EMAIL = 'customer_email';
    public const CUSTOMER_NAME = 'customer_name';
    public const PRODUCT_NAME = 'product_name';
    public const PRODUCT_SLUG = 'product_slug';
    public const PRODUCT_SKU = 'product_sku';
    public const LICENSE_TYPE = 'license_type';
    public const STATUS = 'status';
    public const MAX_ACTIVATIONS = 'max_activations';
    public const MAX_ACTIVATIONS_PER_INSTANCE = 'max_activations_per_instance';
    public const CURRENT_ACTIVATIONS = 'current_activations';
    public const EXPIRES_AT = 'expires_at';
    public const METADATA = 'metadata';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
}
