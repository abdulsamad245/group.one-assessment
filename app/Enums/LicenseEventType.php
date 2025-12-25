<?php

namespace App\Enums;

enum LicenseEventType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case ACTIVATED = 'activated';
    case DEACTIVATED = 'deactivated';
    case SUSPENDED = 'suspended';
    case RESUMED = 'resumed';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
    case KEY_GENERATED = 'key_generated';
    case KEY_REVOKED = 'key_revoked';
    case REACTIVATED = 'reactivated';
    case RENEWED = 'renewed';

    /**
     * Get all enum values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum names as an array.
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Check if a value is valid.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'License Created',
            self::UPDATED => 'License Updated',
            self::ACTIVATED => 'License Activated',
            self::DEACTIVATED => 'License Deactivated',
            self::SUSPENDED => 'License Suspended',
            self::RESUMED => 'License Resumed',
            self::EXPIRED => 'License Expired',
            self::REVOKED => 'License Revoked',
            self::KEY_GENERATED => 'License Key Generated',
            self::KEY_REVOKED => 'License Key Revoked',
            self::REACTIVATED => 'License Reactivated',
            self::RENEWED => 'License Renewed',
        };
    }
}
