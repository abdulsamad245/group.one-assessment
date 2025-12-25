<?php

return [
    // General
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'not-found' => 'Resource not found',
    'unauthorized' => 'Unauthorized access',
    'forbidden' => 'Access forbidden',
    'validation-error' => 'Validation error',
    'server-error' => 'Internal server error',

    // Brands
    'brand-created' => 'Brand created successfully',
    'brand-updated' => 'Brand updated successfully',
    'brand-deleted' => 'Brand deleted successfully',
    'brand-found' => 'Brand retrieved successfully',
    'brands-found' => 'Brands retrieved successfully',
    'brand-not-found' => 'Brand not found',

    // Licenses
    'license-created' => 'License created successfully',
    'license-updated' => 'License updated successfully',
    'license-deleted' => 'License deleted successfully',
    'license-found' => 'License retrieved successfully',
    'licenses-found' => 'Licenses retrieved successfully',
    'license-not-found' => 'License not found',
    'license-suspended' => 'License suspended successfully',
    'license-reactivated' => 'License reactivated successfully',
    'license-renewed' => 'License renewed successfully',
    'license-resumed' => 'License resumed successfully',
    'license-canceled' => 'License canceled successfully',
    'license-cannot-activate' => 'License cannot be activated. Maximum activations reached or license is not active',
    'license-expired' => 'License has expired',
    'license-not-active' => 'License is not active',

    // License Keys
    'license-key-created' => 'License key created successfully',
    'license-key-generated' => 'License key generated successfully',
    'license-key-found' => 'License key retrieved successfully',
    'license-keys-found' => 'License keys retrieved successfully',
    'license-key-not-found' => 'License key not found',
    'license-key-invalid' => 'Invalid license key',
    'license-key-not-valid' => 'License key is not valid or has expired',
    'license-key-revoked' => 'License key has been revoked',

    // Activations
    'activation-created' => 'License activated successfully',
    'activation-deactivated' => 'License deactivated successfully',
    'activation-found' => 'Activation retrieved successfully',
    'activations-found' => 'Activations retrieved successfully',
    'activation-not-found' => 'Activation not found',
    'activation-status-checked' => 'Activation status checked successfully',
    'activation-already-exists' => 'Device is already activated',
    'activation-limit-reached' => 'Maximum activation limit reached',
    'license-max-activations-reached-for-instance-type' => 'Maximum seats reached for instance type :instance_type (max: :max)',
    'instance-type-not-configured' => 'Instance type :instance_type is not configured for this license',
    'license-not-found-for-product' => 'No license found for the specified product',
    'product-name-required' => 'Product name is required',
    'product-slug-required' => 'Product slug is required',

    // Customers
    'customer-licenses-found' => 'Customer licenses retrieved successfully',
    'customer-not-found' => 'Customer not found',

    // Records
    'record-created' => 'Record created successfully',
    'record-updated' => 'Record updated successfully',
    'record-deleted' => 'Record deleted successfully',
    'record-found' => 'Record retrieved successfully',
    'records-found' => 'Records retrieved successfully',
    'record-not-found' => 'Record not found',

    // Auth messages
    'user-registered' => 'User registered successfully',
    'user-logged-in' => 'User logged in successfully',
    'user-logged-out' => 'User logged out successfully',
    'user-retrieved' => 'User retrieved successfully',
    'invalid-credentials' => 'Invalid email or password',

    // API Key messages
    'api-key-created' => 'API key created successfully',
    'api-key-rotated' => 'API key rotated successfully',
    'api-key-revoked' => 'API key revoked successfully',
    'api-keys-retrieved' => 'API keys retrieved successfully',
    'api-key-not-found' => 'API key not found',

    // Validation
    'invalid-input' => 'Invalid input provided',
    'required-field' => 'This field is required',
    'invalid-email' => 'Invalid email address',
    'invalid-date' => 'Invalid date format',
    'email-required' => 'Email address is required',
    'email-invalid' => 'Please provide a valid email address',
    'license-key-required' => 'License key is required',
    'device-identifier-required' => 'Device identifier is required',
    'license-id-required' => 'License ID is required',
    'activation-id-required' => 'Activation ID is required',
    'activation-id-invalid' => 'Activation ID must be a valid integer',
];

