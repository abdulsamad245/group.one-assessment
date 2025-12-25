# Implementation Checklist

## Overview
This document tracks the implementation of the following requirements:
1. **Table-Specific Column Constants** - Replace `DatabaseColumns` with table-specific classes
2. **Request Classes for All Controllers** - Every controller method must have a dedicated Request class
3. **Try-Catch in All Controllers** - All controllers must use try-catch with ApiResponse trait
4. **Sentry Integration** - Configure Sentry for error tracking
5. **Use Constants Everywhere** - Replace all hardcoded column names with constants

---

## 1. Table-Specific Column Constants

### ✅ Created Column Constant Classes

- ✅ `app/Constants/BrandColumns.php`
- ✅ `app/Constants/LicenseColumns.php`
- ✅ `app/Constants/LicenseKeyColumns.php`
- ✅ `app/Constants/ActivationColumns.php`
- ✅ `app/Constants/LicenseEventColumns.php`

### ⏳ Update Migrations

- ✅ `2024_01_01_000001_create_brands_table.php` - Uses `BrandColumns`
- ✅ `2024_01_01_000002_create_licenses_table.php` - Uses `LicenseColumns` and `BrandColumns`
- ⏳ `2024_01_01_000003_create_license_keys_table.php` - Needs update
- ⏳ `2024_01_01_000004_create_activations_table.php` - Needs update
- ⏳ `2024_01_01_000005_create_license_events_table.php` - Needs update

### ⏳ Update Models

- ⏳ `app/Models/Brand.php` - Use `BrandColumns` for fillable, casts, etc.
- ⏳ `app/Models/License.php` - Use `LicenseColumns`
- ⏳ `app/Models/LicenseKey.php` - Use `LicenseKeyColumns`
- ⏳ `app/Models/Activation.php` - Use `ActivationColumns`
- ⏳ `app/Models/LicenseEvent.php` - Use `LicenseEventColumns`

### ⏳ Update Repositories

- ⏳ `app/Repositories/BrandRepository.php`
- ⏳ `app/Repositories/LicenseRepository.php`
- ⏳ `app/Repositories/LicenseKeyRepository.php`
- ⏳ `app/Repositories/ActivationRepository.php`
- ⏳ `app/Repositories/LicenseEventRepository.php`

### ⏳ Update Services

- ⏳ `app/Services/LicenseService.php`
- ⏳ `app/Services/ActivationService.php`
- ⏳ `app/Services/CustomerService.php`
- ⏳ `app/Services/LicenseEventService.php`

### ⏳ Update Factories

- ⏳ `database/factories/BrandFactory.php`
- ⏳ `database/factories/LicenseFactory.php`
- ⏳ `database/factories/LicenseKeyFactory.php`
- ⏳ `database/factories/ActivationFactory.php`

### ⏳ Update Seeders

- ⏳ `database/seeders/BrandSeeder.php`
- ⏳ `database/seeders/LicenseSeeder.php`
- ⏳ `database/seeders/LicenseKeySeeder.php`
- ⏳ `database/seeders/ActivationSeeder.php`

### ⏳ Update Resources

- ⏳ `app/Http/Resources/BrandResource.php`
- ⏳ `app/Http/Resources/LicenseResource.php`
- ⏳ `app/Http/Resources/LicenseKeyResource.php`
- ⏳ `app/Http/Resources/ActivationResource.php`

---

## 2. Request Classes for All Controllers

### ✅ Created Request Classes

- ✅ `app/Http/Requests/StoreBrandRequest.php` - Already exists
- ✅ `app/Http/Requests/UpdateBrandRequest.php` - Already exists
- ✅ `app/Http/Requests/StoreLicenseRequest.php` - Already exists
- ✅ `app/Http/Requests/UpdateLicenseRequest.php` - Already exists
- ✅ `app/Http/Requests/ActivateLicenseRequest.php` - Already exists
- ✅ `app/Http/Requests/StoreLicenseKeyRequest.php` - Created
- ✅ `app/Http/Requests/GetCustomerLicensesRequest.php` - Created
- ✅ `app/Http/Requests/CheckActivationStatusRequest.php` - Created
- ✅ `app/Http/Requests/DeactivateLicenseRequest.php` - Created

### ⏳ Update Controllers to Use Request Classes

- ⏳ `BrandController::index()` - Needs Request class (if filtering)
- ⏳ `BrandController::show()` - No validation needed (route model binding)
- ⏳ `BrandController::destroy()` - No validation needed
- ⏳ `LicenseKeyController::index()` - Needs Request class (if filtering)
- ⏳ `LicenseKeyController::store()` - Use `StoreLicenseKeyRequest`
- ⏳ `LicenseKeyController::show()` - No validation needed
- ✅ `CustomerController::licenses()` - Uses `GetCustomerLicensesRequest`
- ⏳ `ActivationController::status()` - Use `CheckActivationStatusRequest`
- ⏳ `ActivationController::deactivate()` - Use `DeactivateLicenseRequest`

---

## 3. Try-Catch in All Controllers

### ✅ Controllers with Try-Catch + ApiResponse

- ✅ `LicenseController` - All methods use try-catch
- ✅ `ActivationController` - All methods use try-catch
- ✅ `CustomerController` - Updated with try-catch

### ⏳ Controllers Needing Try-Catch + ApiResponse

- ⏳ `BrandController` - All methods need try-catch
- ⏳ `LicenseKeyController` - All methods need try-catch

---

## 4. Sentry Integration

### ✅ Sentry Setup

- ✅ Installed `sentry/sentry-laravel` package
- ✅ Published Sentry config file to `config/sentry.php`

### ⏳ Sentry Configuration

- ⏳ Add `SENTRY_LARAVEL_DSN` to `.env.example`
- ⏳ Update `config/sentry.php` with project-specific settings
- ⏳ Add Sentry middleware to `bootstrap/app.php` or `app/Http/Kernel.php`
- ⏳ Test Sentry integration with test exception

---

## 5. Translation Messages

### ✅ Added Messages

- ✅ `email-required`
- ✅ `email-invalid`
- ✅ `license-key-required`
- ✅ `device-identifier-required`
- ✅ `license-id-required`
- ✅ `brand-not-found`

---

## 6. Files to Delete

### ⏳ Old Constant Files (After Migration Complete)

- ⏳ `app/Constants/DatabaseColumns.php` - Delete after all files updated
- ⏳ `app/Constants/LicenseType.php` - Already replaced by Enum
- ⏳ `app/Constants/LicenseStatus.php` - Already replaced by Enum
- ⏳ `app/Constants/LicenseKeyStatus.php` - Already replaced by Enum
- ⏳ `app/Constants/ActivationStatus.php` - Already replaced by Enum
- ⏳ `app/Constants/LicenseEventType.php` - Already replaced by Enum

---

## Next Steps

1. **Complete Migration Updates** - Update remaining 3 migration files
2. **Update All Models** - Replace hardcoded column names with constants
3. **Update All Repositories** - Use column constants in queries
4. **Update All Services** - Use column constants
5. **Update All Factories** - Use column constants
6. **Update All Seeders** - Use column constants
7. **Update All Resources** - Use column constants
8. **Update Remaining Controllers** - Add try-catch and ApiResponse
9. **Configure Sentry** - Add DSN and test
10. **Delete Old Files** - Remove deprecated constant files

---

## Testing Checklist

After all updates:

- [ ] Run migrations: `php artisan migrate:fresh --seed`
- [ ] Run tests: `php artisan test`
- [ ] Run PHPStan: `composer phpstan`
- [ ] Test Sentry: Trigger test exception
- [ ] Test all API endpoints with Postman
- [ ] Verify all validation messages use translations
- [ ] Verify all responses use uniform structure


