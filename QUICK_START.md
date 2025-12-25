# Quick Start Guide

## 🚀 Getting Started

### 1. Run Migrations

```bash
sail artisan migrate
```

This creates the `api_keys` table needed for authentication.

### 2. Create Your First API Key

```bash
# Generate an API key for brand ID 1
sail artisan apikey:generate 1 "My First API Key"
```

**Output:**
```
✓ API Key generated successfully!

API Key ID: 1
Brand: Test Brand (ID: 1)
Name: My First API Key
Key: lcs_abc12345.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

⚠️  IMPORTANT: Save this key now! It will not be shown again.
```

**Copy and save the key immediately!**

### 3. Test the API

```bash
# Replace YOUR_API_KEY with the key from step 2
curl -H "Authorization: Bearer YOUR_API_KEY" \
     http://localhost/api/v1/brands
```

### 4. View API Documentation

Generate Swagger docs:
```bash
sail artisan l5-swagger:generate
```

Open in browser:
```
http://localhost/api/documentation
```

### 5. Run Tests

```bash
sail artisan test
```

## 📋 Common Commands

### API Key Management

```bash
# List all API keys
sail artisan apikey:list

# List keys for specific brand
sail artisan apikey:list 1

# List including inactive/expired keys
sail artisan apikey:list --all

# Generate key with expiration (90 days)
sail artisan apikey:generate 1 "Temporary Key" --expires-in=90

# Generate key with permissions
sail artisan apikey:generate 1 "Limited Key" --permissions=read,write

# Revoke (deactivate) an API key
sail artisan apikey:revoke 1

# Permanently delete an API key
sail artisan apikey:revoke 1 --delete
```

### Testing

```bash
# Run all tests
sail artisan test

# Run only feature tests
sail artisan test --testsuite=Feature

# Run only unit tests
sail artisan test --testsuite=Unit

# Run with coverage
sail artisan test --coverage

# Run specific test file
sail artisan test tests/Feature/BrandApiTest.php
```

### Documentation

```bash
# Generate Swagger documentation
sail artisan l5-swagger:generate

# Clear Swagger cache
sail artisan l5-swagger:generate --force
```

## 🔑 Using API Keys

### In HTTP Headers (Recommended)

```bash
Authorization: Bearer lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Alternative Header

```bash
X-API-Key: lcs_xxxxxxxx.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Example with cURL

```bash
curl -X GET \
  http://localhost/api/v1/brands \
  -H "Authorization: Bearer lcs_abc12345.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

### Example with Postman

1. Open Postman
2. Create new request
3. Set URL: `http://localhost/api/v1/brands`
4. Go to "Authorization" tab
5. Select "Bearer Token"
6. Paste your API key

## 📚 API Endpoints

### Brands
- `GET /api/v1/brands` - List all brands
- `POST /api/v1/brands` - Create a brand
- `GET /api/v1/brands/{id}` - Get a brand
- `PUT /api/v1/brands/{id}` - Update a brand
- `DELETE /api/v1/brands/{id}` - Delete a brand

### Licenses
- `GET /api/v1/licenses` - List licenses (paginated)
- `POST /api/v1/licenses` - Create a license
- `GET /api/v1/licenses/{id}` - Get a license
- `PUT /api/v1/licenses/{id}` - Update a license

### License Keys
- `GET /api/v1/license-keys` - List license keys
- `POST /api/v1/license-keys` - Generate a license key
- `GET /api/v1/license-keys/{id}` - Get a license key

### Activations
- `POST /api/v1/activations` - Activate a license
- `POST /api/v1/deactivations` - Deactivate a license
- `GET /api/v1/activations/status` - Check activation status

### Customers
- `GET /api/v1/customers/licenses?email={email}` - Get customer licenses

## 🔒 Security Notes

1. **Never commit API keys** to version control
2. **Use HTTPS** in production
3. **Rotate keys regularly** - set expiration dates
4. **Revoke compromised keys** immediately
5. **Monitor usage** - check `last_used_at` timestamps

## 📖 More Information

- **API_DOCUMENTATION.md** - Complete API documentation
- **IMPLEMENTATION_SUMMARY.md** - Technical implementation details
- **Swagger UI** - Interactive API docs at `/api/documentation`

## 🆘 Troubleshooting

### "Permission denied" when running sail

```bash
chmod +x vendor/laravel/sail/bin/sail
chmod +x vendor/bin/sail
```

### "API key not found" error

Make sure you're using the correct header format:
```
Authorization: Bearer YOUR_KEY
```

### Tests failing

Make sure migrations are run in test environment:
```bash
sail artisan test --env=testing
```

### Swagger docs not updating

Clear and regenerate:
```bash
sail artisan l5-swagger:generate --force
```

