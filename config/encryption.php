<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the encryption configuration for the group.one Centralized
    | License Service. Sensitive data is encrypted at rest using Laravel's
    | built-in encryption features.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Encrypted Fields
    |--------------------------------------------------------------------------
    |
    | List of model fields that are automatically encrypted at rest.
    | These fields use Laravel's 'encrypted' cast type.
    |
    */

    'encrypted_fields' => [
        'licenses' => [
            'customer_email', // PII - Personal Identifiable Information
        ],

        'license_keys' => [
            'key', // Sensitive - License key value
        ],

        'activations' => [
            'device_identifier', // PII - Device fingerprint
            'ip_address', // PII - IP address
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Algorithm
    |--------------------------------------------------------------------------
    |
    | Laravel uses AES-256-CBC encryption by default. This is configured
    | in the main app configuration (config/app.php).
    |
    | Cipher: AES-256-CBC
    | Key: APP_KEY environment variable (32 characters for AES-256)
    |
    */

    'cipher' => env('APP_CIPHER', 'AES-256-CBC'),

    /*
    |--------------------------------------------------------------------------
    | Database Encryption at Rest
    |--------------------------------------------------------------------------
    |
    | For production environments, enable database-level encryption at rest
    | in addition to application-level encryption.
    |
    | MySQL 8.0+: InnoDB tablespace encryption
    | PostgreSQL: Transparent Data Encryption (TDE)
    |
    */

    'database_encryption' => [
        'enabled' => env('DB_ENCRYPTION_ENABLED', false),
        'algorithm' => env('DB_ENCRYPTION_ALGORITHM', 'AES-256'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption in Transit
    |--------------------------------------------------------------------------
    |
    | All API communication should use HTTPS/TLS to encrypt data in transit.
    | This is enforced at the web server/load balancer level.
    |
    */

    'in_transit' => [
        'force_https' => env('FORCE_HTTPS', true),
        'tls_version' => env('TLS_VERSION', '1.2'), // Minimum TLS 1.2
    ],

    /*
    |--------------------------------------------------------------------------
    | Key Rotation
    |--------------------------------------------------------------------------
    |
    | Configuration for encryption key rotation. When rotating keys,
    | old encrypted data must be re-encrypted with the new key.
    |
    */

    'key_rotation' => [
        'enabled' => env('ENCRYPTION_KEY_ROTATION', false),
        'schedule' => env('ENCRYPTION_KEY_ROTATION_SCHEDULE', '90 days'),
        'previous_keys' => env('ENCRYPTION_PREVIOUS_KEYS', []),
    ],

    /*
    |--------------------------------------------------------------------------
    | Searchable Encryption (Future Enhancement)
    |--------------------------------------------------------------------------
    |
    | For fields that need to be both encrypted and searchable (e.g., email),
    | consider using deterministic encryption or hashing.
    |
    | Current implementation: Standard encryption (not searchable)
    | Future: Implement searchable encryption for customer_email
    |
    */

    'searchable' => [
        'enabled' => env('SEARCHABLE_ENCRYPTION', false),
        'fields' => [
            // 'customer_email' => 'hash', // Use hashing for searchability
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance
    |--------------------------------------------------------------------------
    |
    | Encryption settings to meet compliance requirements (GDPR, PCI-DSS, etc.)
    |
    */

    'compliance' => [
        'gdpr' => [
            'enabled' => env('GDPR_COMPLIANCE', true),
            'encrypt_pii' => true, // Encrypt all PII fields
        ],

        'pci_dss' => [
            'enabled' => env('PCI_DSS_COMPLIANCE', false),
            'encrypt_payment_data' => true,
        ],
    ],

];
