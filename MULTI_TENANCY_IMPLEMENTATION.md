# Multi-Tenancy Implementation

**Date:** 2025-12-21  
**Status:** ✅ **IMPLEMENTED**

---

## Overview

The group.one Centralized License Service implements **multi-tenancy** using a **brand-based isolation model**. Each brand represents a separate tenant with its own licenses, API keys, and data isolation.

---

## Architecture

### **Tenancy Model: Brand-Based Multi-Tenancy**

```
┌─────────────────────────────────────────────────────────┐
│                  Shared Database                         │
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Brand 1    │  │   Brand 2    │  │   Brand 3    │  │
│  │  (WP Rocket) │  │  (Imagify)   │  │  (WP Media)  │  │
│  ├──────────────┤  ├──────────────┤  ├──────────────┤  │
│  │ API Keys     │  │ API Keys     │  │ API Keys     │  │
│  │ Licenses     │  │ Licenses     │  │ Licenses     │  │
│  │ License Keys │  │ License Keys │  │ License Keys │  │
│  │ Activations  │  │ Activations  │  │ Activations  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

**Key Characteristics:**
- ✅ **Shared Database** - All tenants share the same database
- ✅ **Brand Scoping** - Data is scoped by `brand_id` foreign key
- ✅ **API Key Authentication** - Each brand has its own API keys
- ✅ **Data Isolation** - Middleware ensures brand-level data access
- ✅ **Soft Deletes** - All models use soft deletes for data safety

---

## Implementation Details

### **1. Brand Model (Tenant)**

**File:** `app/Models/Brand.php`

```php
class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',           // Brand name (unique)
        'slug',           // URL-friendly identifier (unique)
        'description',    // Brand description
        'contact_email',  // Contact email
        'website',        // Brand website
        'settings',       // JSON settings
        'is_active',      // Active status
    ];

    // Relationships
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

**Database Table:** `brands`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | string | Brand name (unique) |
| `slug` | string | URL slug (unique, indexed) |
| `description` | text | Brand description |
| `contact_email` | string | Contact email |
| `website` | string | Brand website |
| `settings` | json | Brand-specific settings |
| `is_active` | boolean | Active status |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |
| `deleted_at` | timestamp | Soft delete timestamp |

---

### **2. API Key Authentication (Tenant Identification)**

**File:** `app/Http/Middleware/AuthenticateApiKey.php`

**How It Works:**

1. **API Key Extraction**
   - Checks `Authorization: Bearer {api_key}` header
   - Checks `X-API-Key: {api_key}` header

2. **API Key Validation**
   - Extracts prefix from API key (e.g., `lcs_abc12345`)
   - Hashes the full API key using SHA-256
   - Looks up API key by prefix and hash
   - Validates API key is active and not expired

3. **Brand Validation**
   - Checks if associated brand exists
   - Validates brand is active (`is_active = true`)

4. **Request Scoping**
   - Attaches brand information to request:
     ```php
     $request->merge([
         'authenticated_brand_id' => $apiKeyModel->brand_id,
         'authenticated_brand' => $apiKeyModel->brand,
         'api_key_id' => $apiKeyModel->id,
     ]);
     ```
   - Sets global brand context:
     ```php
     app()->instance('current_brand', $apiKeyModel->brand);
     ```

5. **Usage Tracking**
   - Updates `last_used_at` timestamp asynchronously

**API Key Format:**
```
lcs_abc12345.1234567890abcdef1234567890abcdef12345678
│   │        │
│   │        └─ Secret (40 characters)
│   └────────── Prefix (8 characters)
└────────────── Namespace (lcs = License Service)
```

---

### **3. Data Scoping**

All tenant-specific models have a `brand_id` foreign key:

**License Model:**
```php
class License extends Model
{
    protected $fillable = [
        'brand_id',        // ← Tenant identifier
        'customer_email',
        'customer_name',
        // ...
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
```

**Database Relationships:**
```
brands (1) ──→ (N) api_keys
brands (1) ──→ (N) licenses
licenses (1) ──→ (N) license_keys
license_keys (1) ──→ (N) activations
```

**Foreign Key Constraints:**
- `api_keys.brand_id` → `brands.id`
- `licenses.brand_id` → `brands.id`
- `license_keys.license_id` → `licenses.id`
- `activations.license_key_id` → `license_keys.id`

---

### **4. Multi-Tenancy Features**

#### **✅ Data Isolation**
- Each brand can only access its own data
- API key authentication ensures brand context
- Middleware attaches brand to every request

#### **✅ Brand-Specific API Keys**
- Each brand has its own API keys
- API keys are scoped to a single brand
- API keys can have custom permissions (future feature)

#### **✅ Customer Data Across Brands**
- Customers can have licenses across multiple brands
- Customer lookup by email returns all brands
- Example: `customer@mailinator.com` can have:
  - WP Rocket license (Brand 1)
  - Imagify license (Brand 2)
  - WP Media license (Brand 3)

#### **✅ Soft Deletes**
- All models use soft deletes
- Deleted data is retained for audit purposes
- Can be restored if needed

#### **✅ Brand Settings**
- Each brand can have custom settings (JSON)
- Settings can include:
  - Rate limits
  - Email templates
  - Webhook URLs
  - Custom branding

---

## How to Use Multi-Tenancy

### **1. Create a Brand (Tenant)**

```http
POST /api/v1/brands
Content-Type: application/json

{
  "name": "WP Rocket",
  "description": "WordPress caching plugin",
  "contact_email": "support@wp-rocket.me",
  "website": "https://wp-rocket.me",
  "is_active": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Brand created successfully",
  "data": {
    "id": 1,
    "name": "WP Rocket",
    "slug": "wp-rocket",
    "is_active": true
  }
}
```

### **2. Generate API Key for Brand**

```php
// In your application or seeder
$brand = Brand::find(1);

// Generate API key
$plainKey = ApiKey::generate(); // Returns: lcs_abc12345.secret...

// Store hashed key
$apiKey = ApiKey::create([
    'brand_id' => $brand->id,
    'name' => 'Production API Key',
    'key' => ApiKey::hash($plainKey),
    'prefix' => ApiKey::extractPrefix($plainKey),
    'is_active' => true,
]);

// Give $plainKey to the brand (store securely!)
```

### **3. Make Authenticated Requests**

```http
GET /api/v1/licenses
Authorization: Bearer lcs_abc12345.secret...
```

**What Happens:**
1. Middleware extracts API key
2. Validates API key and brand
3. Attaches brand context to request
4. Controller accesses brand via `$request->authenticated_brand`
5. All queries are automatically scoped to this brand

---

## Security Features

### **✅ API Key Hashing**
- API keys are hashed using SHA-256
- Only hashed keys are stored in database
- Plain keys are never stored

### **✅ Prefix-Based Lookup**
- API keys have indexed prefixes
- Fast lookup without full table scan
- Prevents timing attacks

### **✅ Brand Validation**
- Inactive brands cannot authenticate
- Expired API keys are rejected
- Soft-deleted brands are inaccessible

### **✅ Rate Limiting**
- Global rate limit: 60 requests/minute per API key
- Endpoint-specific limits
- Prevents abuse and DDoS attacks

### **✅ Data Encryption**
- Customer emails are encrypted at rest
- Uses Laravel's encrypted cast (AES-256-CBC)
- Searchable encryption for queries

---

## Multi-Tenancy Status

| Feature | Status | Implementation |
|---------|--------|----------------|
| **Brand Model** | ✅ Implemented | `app/Models/Brand.php` |
| **API Key Authentication** | ✅ Implemented | `app/Http/Middleware/AuthenticateApiKey.php` |
| **Data Scoping** | ✅ Implemented | Foreign keys on all models |
| **Brand Context** | ✅ Implemented | Middleware attaches brand to request |
| **Soft Deletes** | ✅ Implemented | All models use SoftDeletes trait |
| **Rate Limiting** | ✅ Implemented | Global + endpoint-specific |
| **Data Encryption** | ✅ Implemented | Customer PII encrypted |
| **Cross-Brand Queries** | ✅ Implemented | Customer service supports multi-brand |

---

## Summary

**Multi-Tenancy Implementation:**
- ✅ **Brand-based tenancy** - Each brand is a separate tenant
- ✅ **Shared database** - All tenants in one database
- ✅ **API key authentication** - Brand identified by API key
- ✅ **Data isolation** - Foreign key scoping ensures separation
- ✅ **Middleware enforcement** - Every request is brand-scoped
- ✅ **Security** - Hashed API keys, encryption, rate limiting
- ✅ **Flexibility** - Customers can have licenses across brands

**Architecture Pattern:** **Shared Database, Shared Schema, Brand-Scoped Data**

**End of Multi-Tenancy Documentation**

