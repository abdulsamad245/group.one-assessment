<?php

namespace App\Constants;

/**
 * API Key table column constants.
 */
class ApiKeyConstant
{
    // Table name
    public const TABLE = 'api_keys';

    // Primary key
    public const ID = 'id';

    // Foreign keys
    public const BRAND_ID = 'brand_id';

    // Columns
    public const NAME = 'name';
    public const KEY = 'key';
    public const PREFIX = 'prefix';
    public const PERMISSIONS = 'permissions';
    public const LAST_USED_AT = 'last_used_at';
    public const EXPIRES_AT = 'expires_at';
    public const IS_ACTIVE = 'is_active';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
}
