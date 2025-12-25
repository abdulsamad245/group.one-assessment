# Postman Collection for group.one Centralized License Service

This directory contains the Postman collection and environment files for testing the group.one Centralized License Service API.

## Files

- **group.one Centralized_License_Service.postman_collection.json** - Complete API collection with all endpoints
- **Local_Environment.postman_environment.json** - Environment variables for local development

## Import Instructions

### 1. Import Collection

1. Open Postman
2. Click **Import** button (top left)
3. Select **File** tab
4. Choose `group.one Centralized_License_Service.postman_collection.json`
5. Click **Import**

### 2. Import Environment

1. Click **Import** button
2. Select **File** tab
3. Choose `Local_Environment.postman_environment.json`
4. Click **Import**

### 3. Select Environment

1. Click the environment dropdown (top right)
2. Select **Local Environment**

## Collection Structure

The collection is organized into the following folders:

### 1. Brands
- **List All Brands** - GET all brands
- **Create Brand** - POST new brand
- **Get Brand by ID** - GET specific brand
- **Update Brand** - PUT update brand
- **Delete Brand** - DELETE brand (soft delete)

### 2. Licenses
- **List All Licenses** - GET all licenses
- **Create License** - POST new license
- **Get License by ID** - GET specific license
- **Update License** - PUT update license

### 3. License Keys
- **List All License Keys** - GET all license keys
- **Generate License Key** - POST generate new key
- **Get License Key by ID** - GET specific key

### 4. Activations
- **Activate License** - POST activate a license
- **Deactivate License** - POST deactivate a license
- **Check Activation Status** - GET check license status

### 5. Customers
- **Get Customer Licenses** - GET all licenses for a customer by email

## Environment Variables

The environment includes the following variables:

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost` | API base URL |
| `brand_id` | `1` | Sample brand ID |
| `license_id` | `1` | Sample license ID |
| `license_key_id` | `1` | Sample license key ID |
| `license_key` | `` | License key string |
| `activation_id` | `1` | Sample activation ID |
| `customer_email` | `john.doe@mailinator.com` | Sample customer email |

## Usage Examples

### Creating a Brand

```http
POST {{base_url}}/api/v1/brands
Content-Type: application/json

{
  "name": "Acme Corporation",
  "slug": "acme-corp",
  "description": "Leading provider of enterprise software solutions",
  "contact_email": "contact@acme-corp.com",
  "website": "https://acme-corp.com",
  "is_active": true,
  "settings": {
    "theme": "blue",
    "timezone": "America/New_York"
  }
}
```

### Creating a License

```http
POST {{base_url}}/api/v1/licenses
Content-Type: application/json

{
  "brand_id": 1,
  "customer_email": "john.doe@mailinator.com",
  "customer_name": "John Doe",
  "product_name": "Premium Software Suite",
  "product_sku": "PREM-001",
  "license_type": "subscription",
  "max_activations": 5,
  "expires_at": "2025-12-31 23:59:59",
  "metadata": {
    "source": "web",
    "campaign": "summer-sale"
  }
}
```

### Activating a License

```http
POST {{base_url}}/api/v1/activations
Content-Type: application/json

{
  "license_key": "XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX",
  "device_identifier": "DEVICE-12345",
  "device_name": "John's MacBook Pro",
  "ip_address": "192.168.1.100",
  "user_agent": "Mozilla/5.0"
}
```

## Testing Workflow

### 1. Setup

1. Start the application: `./vendor/bin/sail up -d`
2. Run migrations: `./vendor/bin/sail artisan migrate`
3. Seed database: `./vendor/bin/sail artisan db:seed`

### 2. Test Sequence

1. **Create a Brand** - Use "Create Brand" request
2. **Copy Brand ID** - From response, update `brand_id` variable
3. **Create a License** - Use "Create License" request with the brand_id
4. **Copy License ID** - From response, update `license_id` variable
5. **Generate License Key** - Use "Generate License Key" request
6. **Copy License Key** - From response, update `license_key` variable
7. **Activate License** - Use "Activate License" request with the license_key
8. **Check Status** - Use "Check Activation Status" request
9. **Deactivate** - Use "Deactivate License" request

### 3. Customer Lookup

1. Use "Get Customer Licenses" request
2. Update `customer_email` query parameter
3. View all licenses across brands for that customer

## Tips

- Use Postman's **Tests** tab to automatically extract IDs from responses
- Use **Pre-request Scripts** to generate dynamic data
- Save responses as **Examples** for documentation
- Use **Collection Runner** for automated testing

## Troubleshooting

### Connection Refused

- Ensure Docker containers are running: `./vendor/bin/sail ps`
- Check application is accessible: `curl http://localhost`

### 404 Not Found

- Verify routes are registered: `./vendor/bin/sail artisan route:list`
- Check API version in URL: `/api/v1/`

### Validation Errors

- Check request body matches the expected format
- Verify all required fields are included
- Check data types (strings, integers, booleans)

## Additional Resources

- [API Documentation](http://localhost/api/documentation) - Swagger UI
- [README.md](../README.md) - Project documentation
- [QUICK_REFERENCE.md](../QUICK_REFERENCE.md) - Common commands

