<?php

namespace App\Constants;

/**
 * User table column constants.
 */
class UserConstant
{
    // Table name
    public const TABLE = 'users';

    // Primary key
    public const ID = 'id';

    // Foreign keys
    public const BRAND_ID = 'brand_id';

    // Columns
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const ROLE = 'role';
    public const REMEMBER_TOKEN = 'remember_token';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
}
