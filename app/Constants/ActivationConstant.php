<?php

namespace App\Constants;

/**
 * Activation table column constants.
 */
class ActivationConstant
{
    // Table name
    public const TABLE = 'activations';

    // Primary key
    public const ID = 'id';

    // Foreign keys
    public const LICENSE_KEY_ID = 'license_key_id';

    // Columns
    public const DEVICE_IDENTIFIER = 'device_identifier';
    public const DEVICE_NAME = 'device_name';
    public const INSTANCE_TYPE = 'instance_type';
    public const INSTANCE_VALUE = 'instance_value';
    public const IP_ADDRESS = 'ip_address';
    public const USER_AGENT = 'user_agent';
    public const STATUS = 'status';
    public const ACTIVATED_AT = 'activated_at';
    public const DEACTIVATED_AT = 'deactivated_at';
    public const LAST_CHECKED_AT = 'last_checked_at';
    public const METADATA = 'metadata';

    // Timestamps
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
