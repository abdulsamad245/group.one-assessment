<?php

namespace App\Enums;

enum LicenseType: string
{
    case PERPETUAL = 'perpetual';
    case SUBSCRIPTION = 'subscription';
    case TRIAL = 'trial';

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
        return self::tryFrom($value) !== null;
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PERPETUAL => 'Perpetual License',
            self::SUBSCRIPTION => 'Subscription License',
            self::TRIAL => 'Trial License',
        };
    }
}
