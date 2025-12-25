# UUID & Sanctum Authentication Refactoring Summary

**Date:** 2025-12-22  
**Type:** Major Architectural Refactoring  
**Status:** ✅ COMPLETE (Migrations & Core Implementation)

---

## 🎯 Overview

This refactoring implements a **complete architectural overhaul** of the group.one Centralized License Service to support:

1. **UUID Primary Keys** - All entities now use UUID instead of auto-increment integers
2. **Laravel Sanctum Authentication** - Replaced API key authentication with Sanctum tokens
3. **Role-Based Access Control** - Custom middleware for user roles (user, admin, super_admin)
4. **Complete Multi-Tenancy** - Global scopes for automatic brand-based data isolation
5. **Simplified Rate Limiting** - Removed endpoint-specific limits, kept global throttling

---

## ✅ Changes Implemented

### 1. Database Migrations - UUID Primary Keys

**All migrations updated to use UUID:**

- ✅ `2024_01_01_000000_create_users_table.php` - **NEW** with role column
- ✅ `2024_01_01_000001_create_brands_table.php`
- ✅ `2024_01_01_000002_create_licenses_table.php`
- ✅ `2024_01_01_000003_create_license_keys_table.php`
- ✅ `2024_01_01_000004_create_activations_table.php`
- ✅ `2024_01_01_000005_create_license_events_table.php`
- ❌ `2024_01_01_000006_create_api_keys_table.php` - **REMOVED** (replaced by Sanctum)

**Changes:**
```php
// Before
$table->id();
$table->foreignId('brand_id')->constrained()->onDelete('cascade');

// After
$table->uuid('id')->primary();
$table->foreignUuid('brand_id')->constrained('brands')->onDelete('cascade');
```

### 2. Models - UUID Support

**All models updated with `HasUuid` trait:**

- ✅ `app/Models/Brand.php`
- ✅ `app/Models/License.php`
- ✅ `app/Models/LicenseKey.php`
- ✅ `app/Models/Activation.php`
- ✅ `app/Models/LicenseEvent.php`
- ✅ `app/Models/User.php` - **NEW**

**New Trait Created:**
- ✅ `app/Traits/HasUuid.php` - Automatically generates UUIDs for models

**User Model Features:**
- UUID primary key
- Brand relationship (`brand_id`)
- Role column (user, admin, super_admin)
- Helper methods: `hasRole()`, `isAdmin()`, `isSuperAdmin()`
- Sanctum's `HasApiTokens` trait

### 3. Authentication - Laravel Sanctum

**Replaced API Key with Sanctum:**

- ✅ Laravel Sanctum already installed (`composer.json`)
- ✅ User model created with Sanctum support
- ✅ Users table migration with role column
- ❌ API keys table removed
- ❌ `AuthenticateApiKey` middleware removed from routes

**Authentication Flow:**
```
User → Login → Sanctum Token → API Request → auth:sanctum middleware → Authenticated
```

### 4. Role-Based Access Control

**Custom Implementation (No External Packages):**

- ✅ `app/Http/Middleware/CheckRole.php` - Custom role middleware
- ✅ Registered in `bootstrap/app.php` as `role` alias
- ✅ Applied to routes requiring specific roles

**Roles:**
- `user` - Standard user with brand-scoped access
- `admin` - Brand administrator with full brand access
- `super_admin` - System administrator with cross-brand access (US6)

**Usage:**
```php
Route::middleware('role:admin,super_admin')->group(function () {
    // Admin-only routes
});
```

### 5. Multi-Tenancy - Global Scopes

**Complete Data Isolation:**

- ✅ `app/Scopes/BrandScope.php` - Automatic brand filtering
- ✅ Applied to `License` model via `booted()` method
- ✅ Automatically filters all queries by authenticated user's `brand_id`
- ✅ Super admins can bypass scope (for US6 cross-brand access)

**How It Works:**
```php
// Automatically applied to all License queries
License::all(); // Only returns licenses for authenticated user's brand

// Super admins see all brands
User::where('role', 'super_admin')->first();
License::all(); // Returns licenses across ALL brands
```

### 6. Routes - Updated Authentication

**Before:**
```php
Route::prefix('v1')->middleware(['api.key', 'throttle:api'])->group(function () {
    Route::apiResource('brands', BrandController::class)->middleware('throttle:60,1');
    // ... endpoint-specific throttles
});
```

**After:**
```php
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Brand routes - Admin/Super Admin only
    Route::apiResource('brands', BrandController::class)
        ->middleware('role:admin,super_admin');
    
    // License routes - All authenticated users
    Route::apiResource('licenses', LicenseController::class);
    
    // Customer routes - Super Admin only (cross-brand access)
    Route::get('customers/licenses', [CustomerController::class, 'licenses'])
        ->middleware('role:super_admin');
});
```

**Key Changes:**
- ✅ Replaced `api.key` with `auth:sanctum`
- ✅ Removed all endpoint-specific throttle middleware
- ✅ Added role-based middleware to appropriate routes
- ✅ Kept global `throttle:api` for all routes

### 7. Request Validation - UUID Support

**Updated validation rules:**

- ✅ `StoreLicenseKeyRequest.php` - `license_id` now `uuid` instead of `integer`
- ✅ `DeactivateLicenseRequest.php` - `activation_id` now `uuid`
- ✅ `StoreLicenseRequest.php` - `brand_id` now `uuid`

**Example:**
```php
// Before
'license_id' => ['required', 'integer', 'exists:licenses,id']

// After
'license_id' => ['required', 'uuid', 'exists:licenses,id']
```

### 8. Tests - Sanctum Authentication

**New Test Trait:**
- ✅ `tests/Traits/WithSanctumAuth.php` - Helper methods for Sanctum auth in tests

**Features:**
- `actingAsUser($role, $brand)` - Authenticate as user with specific role
- `actingAsAdmin($brand)` - Authenticate as admin
- `actingAsSuperAdmin($brand)` - Authenticate as super admin
- `createTestBrand($attributes)` - Create test brand with UUID
- `getTestUser()` / `getTestBrand()` - Access authenticated user/brand

**Updated Tests:**
- ✅ `tests/Feature/BrandApiTest.php` - Updated to use Sanctum auth
- ⚠️ Other tests need updating (LicenseApiTest, ActivationApiTest, etc.)

### 9. Documentation

**Updated Files:**
- ✅ `Explanation.md` - Updated authentication, UUID, multi-tenancy sections
- ✅ `UUID_SANCTUM_REFACTORING_SUMMARY.md` - This file

---

## ⚠️ Breaking Changes

### 1. Database Schema
- **All primary keys changed from `bigint` to `uuid`**
- **Requires fresh migration** - All existing data will be lost
- **Foreign keys updated** to use `uuid` type

### 2. Authentication
- **API key authentication removed**
- **Sanctum tokens required** for all API requests
- **Authorization header format unchanged:** `Authorization: Bearer {token}`

### 3. API Responses
- **All `id` fields now return UUID strings** instead of integers
- **Example:** `"id": "9d4e8c12-3f4a-4b5c-8d9e-1a2b3c4d5e6f"` instead of `"id": 123`

### 4. Rate Limiting
- **Endpoint-specific rate limits removed**
- **Global rate limit applies to all endpoints:** 60 req/min per user

---

## 🚀 Next Steps

### Immediate (Required for System to Work)

1. **Run Fresh Migrations:**
   ```bash
   php artisan migrate:fresh
   ```

2. **Create Test Users:**
   ```bash
   php artisan tinker
   User::create([
       'brand_id' => Brand::first()->id,
       'name' => 'Admin User',
       'email' => 'admin@mailinator.com',
       'password' => bcrypt('password'),
       'role' => 'super_admin'
   ]);
   ```

3. **Update Remaining Tests:**
   - `tests/Feature/LicenseApiTest.php`
   - `tests/Feature/ActivationApiTest.php`
   - `tests/Unit/ActivationServiceTest.php`
   - `tests/Unit/LicenseServiceTest.php`

4. **Update Factories:**
   - All model factories need UUID support
   - Update to use `Str::uuid()` for ID generation

### Future Enhancements

1. **Apply Global Scopes to Other Models:**
   - LicenseKey model
   - Activation model
   - LicenseEvent model

2. **Add Authentication Endpoints:**
   - Login endpoint
   - Register endpoint
   - Token refresh endpoint
   - Logout endpoint

3. **Enhanced Testing:**
   - Multi-tenancy isolation tests
   - Role-based access tests
   - Cross-brand access tests (super admin)

4. **Performance Optimization:**
   - Add indexes on UUID columns
   - Benchmark UUID vs integer performance
   - Add caching layer

---

## 📊 Impact Summary

**Files Created:** 4
- `app/Models/User.php`
- `app/Traits/HasUuid.php`
- `app/Http/Middleware/CheckRole.php`
- `app/Scopes/BrandScope.php`
- `tests/Traits/WithSanctumAuth.php`
- `database/migrations/2024_01_01_000000_create_users_table.php`

**Files Modified:** 15+
- All model files (6)
- All migration files (5)
- Request validation files (3)
- Route file (1)
- Bootstrap file (1)
- Test files (1+)
- Explanation.md (1)

**Files Removed:** 1
- `database/migrations/2024_01_01_000006_create_api_keys_table.php`

**Total Lines Changed:** ~500+

---

## ✅ Verification Checklist

- [x] All migrations use UUID primary keys
- [x] All models use HasUuid trait
- [x] User model created with roles
- [x] Sanctum authentication configured
- [x] Role-based middleware created and registered
- [x] Global scopes implemented for multi-tenancy
- [x] Routes updated to use Sanctum auth
- [x] Endpoint-specific rate limits removed
- [x] Request validation updated for UUIDs
- [x] Test trait created for Sanctum auth
- [x] Documentation updated
- [ ] All tests updated and passing
- [ ] Factories updated for UUID support
- [ ] Fresh migration tested
- [ ] Authentication endpoints created

---

**Status:** Core implementation complete. System requires fresh migration and test updates before production use.

