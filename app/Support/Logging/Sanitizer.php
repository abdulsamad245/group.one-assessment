<?php

namespace App\Support\Logging;

/**
 * Helper class to sanitize sensitive data for logging.
 */
class Sanitizer
{
    /**
     * Sensitive field names to redact.
     */
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'secret',
        'token',
        'api_key',
        'api_secret',
        'authorization',
        'bearer',
        'credit_card',
        'card_number',
        'cvv',
        'ssn',
        'license_key',
        'key',
    ];

    /**
     * Mask an email address for logging.
     */
    public static function maskEmail(?string $email): ?string
    {
        if (!$email) {
            return $email;
        }

        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return '*****';
        }

        [$name, $domain] = $parts;
        $len = strlen($name);

        if ($len <= 1) {
            return '*@' . $domain;
        }

        return substr($name, 0, 1) . str_repeat('*', $len - 1) . '@' . $domain;
    }

    /**
     * Sanitize a payload array by redacting sensitive values.
     */
    public static function sanitizePayload(array $payload): array
    {
        $sanitize = function (&$value, $key) use (&$sanitize) {
            $lower = strtolower((string)$key);

            if (is_array($value)) {
                array_walk($value, $sanitize);
                return;
            }

            // Check if key contains any sensitive field name
            foreach (self::SENSITIVE_FIELDS as $sensitive) {
                if (str_contains($lower, $sensitive)) {
                    $value = '[REDACTED]';
                    return;
                }
            }

            // Mask email fields
            if ($lower === 'email' || str_contains($lower, '_email') || str_contains($lower, 'email_')) {
                $value = self::maskEmail((string)$value);
                return;
            }
        };

        array_walk($payload, $sanitize);
        return $payload;
    }

    /**
     * Sanitize headers by redacting sensitive values.
     */
    public static function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'x-api-key', 'cookie', 'set-cookie'];
        $sanitized = [];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);
            if (in_array($lowerKey, $sensitiveHeaders)) {
                $sanitized[$key] = ['[REDACTED]'];
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
