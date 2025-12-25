# API Documentation

## Overview

This is a multi-tenant, multi-brand group.one Centralized license management system. All API endpoints require API key authentication.

## Authentication

### API Key Authentication

All API requests must include an API key for authentication. The API key can be provided in two ways:

#### Option 1: Authorization Header (Recommended)
```http
Authorization: Bearer lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### Option 2: X-API-Key Header
```http
X-API-Key: lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Generating API Keys

API keys are associated with brands (tenants). Use the following artisan commands to manage API keys:

#### Generate a new API key
```bash
php artisan apikey:generate {brand_id_or_slug} {name} [--expires-in=days] [--permissions=perm1,perm2]
```

**Examples:**
```bash
# Generate an API key for brand ID 1
php artisan apikey:generate 1 "Production API Key"

# Generate an API key with expiration
php artisan apikey:generate acme-corp "Development Key" --expires-in=90

# Generate an API key with permissions
php artisan apikey:generate 1 "Limited Key" --permissions=read,write
```

#### List API keys
```bash
# List all API keys for a specific brand
php artisan apikey:list {brand_id_or_slug}

# List all API keys across all brands
php artisan apikey:list

# Include inactive and expired keys
php artisan apikey:list --all
```

#### Revoke an API key
```bash
# Deactivate an API key
php artisan apikey:revoke {api_key_id}

# Permanently delete an API key
php artisan apikey:revoke {api_key_id} --delete
```

## API Documentation (Swagger/OpenAPI)

Interactive API documentation is available at:
```
http://your-domain/api/documentation
```

To regenerate the Swagger documentation:
```bash
php artisan l5-swagger:generate
```

### Documentation Structure

API documentation has been organized into separate files for better maintainability:

- `app/Docs/OpenApi.php` - Main OpenAPI configuration and tags
- `app/Docs/BrandDocs.php` - Brand endpoint documentation
- `app/Docs/LicenseDocs.php` - License endpoint documentation
- `app/Docs/ActivationDocs.php` - Activation endpoint documentation
- `app/Docs/Schemas.php` - Reusable schema definitions

This structure keeps controllers clean and makes documentation easier to maintain.

## API Endpoints

### Brands
- `GET /api/v1/brands` - List all brands
- `POST /api/v1/brands` - Create a new brand
- `GET /api/v1/brands/{id}` - Get a specific brand
- `PUT /api/v1/brands/{id}` - Update a brand
- `DELETE /api/v1/brands/{id}` - Delete a brand

### Licenses
- `GET /api/v1/licenses` - List all licenses (paginated)
- `POST /api/v1/licenses` - Create a new license
- `GET /api/v1/licenses/{id}` - Get a specific license
- `PUT /api/v1/licenses/{id}` - Update a license

### License Keys
- `GET /api/v1/license-keys` - List all license keys
- `POST /api/v1/license-keys` - Generate a new license key
- `GET /api/v1/license-keys/{id}` - Get a specific license key

### Activations
- `POST /api/v1/activations` - Activate a license
- `POST /api/v1/deactivations` - Deactivate a license
- `GET /api/v1/activations/status` - Check activation status

### Customers
- `GET /api/v1/customers/licenses` - Get all licenses for a customer by email

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [...],
    "total": 100,
    "per_page": 15,
    "last_page": 7
  }
}
```

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Writing Tests with API Key Authentication

Use the `WithApiKey` trait in your feature tests:

```php
use Tests\Traits\WithApiKey;

class MyApiTest extends TestCase
{
    use RefreshDatabase, WithApiKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApiKey();
    }

    public function test_example(): void
    {
        $response = $this->getJsonWithApiKey('/api/v1/endpoint');
        $response->assertStatus(200);
    }
}
```

## Security Best Practices

1. **Store API keys securely** - Never commit API keys to version control
2. **Use HTTPS** - Always use HTTPS in production to protect API keys in transit
3. **Rotate keys regularly** - Set expiration dates and rotate keys periodically
4. **Limit permissions** - Use the permissions feature to restrict API key capabilities
5. **Monitor usage** - Check the `last_used_at` timestamp to detect unused or compromised keys
6. **Revoke immediately** - If a key is compromised, revoke it immediately

## Database Migrations

Run migrations to set up the API keys table:

```bash
php artisan migrate
```

The API keys migration creates a table with:
- Brand association (tenant scoping)
- Hashed key storage (SHA-256)
- Prefix for fast lookups
- Expiration dates
- Activity tracking
- Soft deletes

## Environment Setup

No additional environment variables are required for API key authentication. The system works out of the box after running migrations.

