<?php

namespace App\Constants;

/**
 * Brand table column constants.
 */
class BrandConstant
{
    // Table name
    public const TABLE = 'brands';

    // Primary key
    public const ID = 'id';

    // Columns
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const DESCRIPTION = 'description';
    public const CONTACT_EMAIL = 'contact_email';
    public const WEBSITE = 'website';
    public const SETTINGS = 'settings';
    public const IS_ACTIVE = 'is_active';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
}
