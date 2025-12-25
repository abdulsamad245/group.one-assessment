# Enums and Database Constants Implementation Guide

## Overview

This document explains the implementation of PHP Enums for type-safe status/type values and database column constants for consistent column naming throughout the group.one Centralized License Service.

---

## 1. PHP Enums (Replacing Constants Classes)

### Why Enums?

PHP 8.1+ Enums provide:
- **Type Safety**: Compile-time checking
- **IDE Support**: Better autocomplete and refactoring
- **Native Support**: Built into PHP, no custom classes needed
- **Pattern Matching**: Use with `match()` expressions
- **Backed Enums**: String/int values for database storage

### Created Enums

#### `app/Enums/LicenseType.php`
```php
enum LicenseType: string
{
    case PERPETUAL = 'perpetual';
    case SUBSCRIPTION = 'subscription';
    case TRIAL = 'trial';
}
```

**Usage:**
```php
// Get the string value
$type = LicenseType::SUBSCRIPTION->value; // 'subscription'

// Get all values as array
$allTypes = LicenseType::values(); // ['perpetual', 'subscription', 'trial']

// Validation rules
Rule::in(LicenseType::values())

// Create from string
$enum = LicenseType::from('subscription');

// Safe creation (returns null if invalid)
$enum = LicenseType::tryFrom('invalid'); // null

// Get human-readable label
$label = LicenseType::SUBSCRIPTION->label(); // 'Subscription License'
```

#### `app/Enums/LicenseStatus.php`
```php
enum LicenseStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
}
```

#### `app/Enums/LicenseKeyStatus.php`
```php
enum LicenseKeyStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';
}
```

#### `app/Enums/ActivationStatus.php`
```php
enum ActivationStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}
```

#### `app/Enums/LicenseEventType.php`
```php
enum LicenseEventType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case ACTIVATED = 'activated';
    case DEACTIVATED = 'deactivated';
    case SUSPENDED = 'suspended';
    case RESUMED = 'resumed';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
    case KEY_GENERATED = 'key_generated';
    case KEY_REVOKED = 'key_revoked';
    case REACTIVATED = 'reactivated';
    case RENEWED = 'renewed';
}
```

### Enum Helper Methods

All enums include these helper methods:

```php
// Get all enum values as array
public static function values(): array

// Get all enum names as array
public static function names(): array

// Check if a value is valid
public static function isValid(string $value): bool

// Get human-readable label
public function label(): string

// Check if status is active (for status enums)
public function isActive(): bool
```

---

## 2. Database Column Constants

### Why Column Constants?

- **Avoid Typos**: Compile-time checking of column names
- **Refactoring**: Easy to rename columns across the entire codebase
- **Consistency**: Single source of truth for column names
- **IDE Support**: Autocomplete for column names

### Created Constants

**File:** `app/Constants/DatabaseColumns.php`

```php
class DatabaseColumns
{
    // Common columns
    public const ID = 'id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    // Brands table
    public const BRANDS_TABLE = 'brands';
    public const BRANDS_NAME = 'name';
    public const BRANDS_SLUG = 'slug';
    public const BRANDS_DESCRIPTION = 'description';
    public const BRANDS_CONTACT_EMAIL = 'contact_email';
    public const BRANDS_WEBSITE = 'website';
    public const BRANDS_SETTINGS = 'settings';
    public const BRANDS_IS_ACTIVE = 'is_active';

    // Licenses table
    public const LICENSES_TABLE = 'licenses';
    public const LICENSES_BRAND_ID = 'brand_id';
    public const LICENSES_CUSTOMER_EMAIL = 'customer_email';
    public const LICENSES_CUSTOMER_NAME = 'customer_name';
    public const LICENSES_PRODUCT_NAME = 'product_name';
    public const LICENSES_LICENSE_TYPE = 'license_type';
    public const LICENSES_STATUS = 'status';
    public const LICENSES_MAX_ACTIVATIONS = 'max_activations';
    public const LICENSES_CURRENT_ACTIVATIONS = 'current_activations';
    public const LICENSES_EXPIRES_AT = 'expires_at';
    public const LICENSES_METADATA = 'metadata';

    // ... and more for other tables
}
```

### Usage in Migrations

```php
use App\Constants\DatabaseColumns as Column;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;

Schema::create(Column::LICENSES_TABLE, function (Blueprint $table) {
    $table->id();
    $table->foreignId(Column::LICENSES_BRAND_ID)
        ->constrained(Column::BRANDS_TABLE)
        ->onDelete('cascade');
    $table->string(Column::LICENSES_CUSTOMER_EMAIL);
    $table->string(Column::LICENSES_CUSTOMER_NAME);
    $table->enum(Column::LICENSES_LICENSE_TYPE, LicenseType::values())
        ->default(LicenseType::SUBSCRIPTION->value);
    $table->enum(Column::LICENSES_STATUS, LicenseStatus::values())
        ->default(LicenseStatus::ACTIVE->value);
    // ...
});
```

---

## 3. Usage Examples

### In Services

```php
use App\Enums\LicenseStatus;
use App\Enums\LicenseEventType;

class LicenseService
{
    public function suspendLicense(License $license): License
    {
        // Use ->value to get the string value
        $updated = $this->updateLicense($license, [
            'status' => LicenseStatus::SUSPENDED->value
        ]);

        $this->eventService->logEvent(
            $updated,
            LicenseEventType::SUSPENDED->value,
            'License suspended'
        );

        return $updated;
    }
}
```

### In Requests

```php
use App\Enums\LicenseType;
use App\Enums\LicenseStatus;
use Illuminate\Validation\Rule;

class StoreLicenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'license_type' => ['required', Rule::in(LicenseType::values())],
            'status' => [Rule::in(LicenseStatus::values())],
        ];
    }
}
```

### In Models

```php
use App\Enums\LicenseStatus;

class License extends Model
{
    public function isActive(): bool
    {
        return $this->status === LicenseStatus::ACTIVE->value;
    }

    public function scopeActive($query)
    {
        return $query->where('status', LicenseStatus::ACTIVE->value);
    }
}
```

---

## 4. Migration from Constants to Enums

### Old Way (Constants)
```php
use App\Constants\LicenseStatus;

// Getting value
$status = LicenseStatus::ACTIVE; // 'active'

// Getting all values
$all = LicenseStatus::ALL; // ['active', 'suspended', ...]

// Validation
Rule::in(LicenseStatus::ALL)
```

### New Way (Enums)
```php
use App\Enums\LicenseStatus;

// Getting value
$status = LicenseStatus::ACTIVE->value; // 'active'

// Getting all values
$all = LicenseStatus::values(); // ['active', 'suspended', ...]

// Validation
Rule::in(LicenseStatus::values())
```

---

## 5. Benefits

### Type Safety
```php
// Old way - no type checking
function setStatus(string $status) { }
setStatus('invalid'); // No error

// New way - type-safe
function setStatus(LicenseStatus $status) { }
setStatus(LicenseStatus::ACTIVE); // ✓
setStatus('invalid'); // ✗ Compile error
```

### Pattern Matching
```php
$message = match($status) {
    LicenseStatus::ACTIVE => 'License is active',
    LicenseStatus::SUSPENDED => 'License is suspended',
    LicenseStatus::EXPIRED => 'License has expired',
    LicenseStatus::REVOKED => 'License was revoked',
};
```

### IDE Support
- Autocomplete for enum cases
- Refactoring support
- Type hints and documentation
- Find usages

---

## 6. Files Modified

### New Files Created
- `app/Enums/LicenseType.php`
- `app/Enums/LicenseStatus.php`
- `app/Enums/LicenseKeyStatus.php`
- `app/Enums/ActivationStatus.php`
- `app/Enums/LicenseEventType.php`
- `app/Constants/DatabaseColumns.php`

### Migrations Updated
- All migrations now use `DatabaseColumns` constants
- All migrations now use Enum `values()` method
- All default values use `->value` property

### Services Updated
- `LicenseService.php` - Uses enums with `->value`
- `ActivationService.php` - Uses enums with `->value`

### Requests Updated
- `StoreLicenseRequest.php` - Uses `Enum::values()`
- `UpdateLicenseRequest.php` - Uses `Enum::values()`

---

## 7. Best Practices

1. **Always use `->value`** when storing enum values in database
2. **Use `Enum::values()`** for validation rules
3. **Use type hints** with enums in function parameters
4. **Use column constants** in migrations, models, and queries
5. **Use `match()`** instead of switch for enum handling

---

## Conclusion

The migration to PHP Enums and database column constants provides:
✅ Type safety throughout the application
✅ Better IDE support and autocomplete
✅ Easier refactoring and maintenance
✅ Consistent column naming
✅ Reduced typos and bugs

