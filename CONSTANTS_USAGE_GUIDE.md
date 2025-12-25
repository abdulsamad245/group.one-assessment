# Constants Usage Guide

This guide explains how to use the constants throughout the group.one Centralized License Service codebase.

## Available Constants

### 1. LicenseStatus (`App\Constants\LicenseStatus`)

Defines all possible license statuses:

```php
use App\Constants\LicenseStatus;

LicenseStatus::ACTIVE      // 'active'
LicenseStatus::SUSPENDED   // 'suspended'
LicenseStatus::EXPIRED     // 'expired'
LicenseStatus::REVOKED     // 'revoked'
LicenseStatus::ALL         // ['active', 'suspended', 'expired', 'revoked']
```

**Usage Examples:**

```php
// Creating a license
$license = License::create([
    'status' => LicenseStatus::ACTIVE,
    // ...
]);

// Querying
$activeLicenses = License::where('status', LicenseStatus::ACTIVE)->get();

// Validation
if (LicenseStatus::isValid($status)) {
    // Valid status
}

// Conditionals
if ($license->status === LicenseStatus::ACTIVE) {
    // License is active
}
```

### 2. LicenseType (`App\Constants\LicenseType`)

Defines all possible license types:

```php
use App\Constants\LicenseType;

LicenseType::PERPETUAL     // 'perpetual'
LicenseType::SUBSCRIPTION  // 'subscription'
LicenseType::TRIAL         // 'trial'
LicenseType::ALL           // ['perpetual', 'subscription', 'trial']
```

**Usage Examples:**

```php
// Creating a license
$license = License::create([
    'license_type' => LicenseType::SUBSCRIPTION,
    // ...
]);

// Conditionals
if ($license->license_type === LicenseType::PERPETUAL) {
    // No expiration date needed
}

// Validation rules
'license_type' => ['required', Rule::in(LicenseType::ALL)],
```

### 3. LicenseKeyStatus (`App\Constants\LicenseKeyStatus`)

Defines all possible license key statuses:

```php
use App\Constants\LicenseKeyStatus;

LicenseKeyStatus::ACTIVE   // 'active'
LicenseKeyStatus::REVOKED  // 'revoked'
LicenseKeyStatus::EXPIRED  // 'expired'
LicenseKeyStatus::ALL      // ['active', 'revoked', 'expired']
```

**Usage Examples:**

```php
// Creating a license key
$licenseKey = LicenseKey::create([
    'status' => LicenseKeyStatus::ACTIVE,
    // ...
]);

// Revoking a key
$licenseKey->update(['status' => LicenseKeyStatus::REVOKED]);

// Querying
$activeKeys = LicenseKey::where('status', LicenseKeyStatus::ACTIVE)->get();
```

### 4. ActivationStatus (`App\Constants\ActivationStatus`)

Defines all possible activation statuses:

```php
use App\Constants\ActivationStatus;

ActivationStatus::ACTIVE       // 'active'
ActivationStatus::DEACTIVATED  // 'deactivated'
ActivationStatus::ALL          // ['active', 'deactivated']
```

**Usage Examples:**

```php
// Creating an activation
$activation = Activation::create([
    'status' => ActivationStatus::ACTIVE,
    // ...
]);

// Deactivating
$activation->update([
    'status' => ActivationStatus::DEACTIVATED,
    'deactivated_at' => now(),
]);

// Querying
$activeActivations = Activation::where('status', ActivationStatus::ACTIVE)->get();
```

### 5. LicenseEventType (`App\Constants\LicenseEventType`)

Defines all possible license event types:

```php
use App\Constants\LicenseEventType;

LicenseEventType::CREATED       // 'created'
LicenseEventType::UPDATED       // 'updated'
LicenseEventType::SUSPENDED     // 'suspended'
LicenseEventType::REACTIVATED   // 'reactivated'
LicenseEventType::RENEWED       // 'renewed'
LicenseEventType::EXPIRED       // 'expired'
LicenseEventType::KEY_GENERATED // 'key_generated'
LicenseEventType::ACTIVATED     // 'activated'
LicenseEventType::DEACTIVATED   // 'deactivated'
LicenseEventType::ALL           // [all event types]
```

**Usage Examples:**

```php
// Logging an event
$eventService->logEvent(
    $license,
    LicenseEventType::CREATED,
    'License created for customer'
);

// Querying events
$createdEvents = LicenseEvent::where('event_type', LicenseEventType::CREATED)->get();
```

## Where Constants Are Used

### Migrations
All enum columns in migrations use constants:
```php
$table->enum('status', LicenseStatus::ALL)->default(LicenseStatus::ACTIVE);
$table->enum('license_type', LicenseType::ALL)->default(LicenseType::SUBSCRIPTION);
```

### Models
Scopes, methods, and conditionals use constants:
```php
public function scopeActive($query)
{
    return $query->where('status', LicenseStatus::ACTIVE);
}
```

### Factories
Default values and states use constants:
```php
'status' => LicenseStatus::ACTIVE,
'license_type' => fake()->randomElement(LicenseType::ALL),
```

### Seeders
All seeded data uses constants:
```php
License::factory()->create([
    'license_type' => LicenseType::SUBSCRIPTION,
    'status' => LicenseStatus::ACTIVE,
]);
```

### Services
Business logic uses constants:
```php
$this->updateLicense($license, ['status' => LicenseStatus::SUSPENDED]);
```

### Requests (Validation)
Validation rules use constants with `Rule::in()`:
```php
'license_type' => ['required', Rule::in(LicenseType::ALL)],
'status' => [Rule::in(LicenseStatus::ALL)],
```

## Benefits

1. **Type Safety**: IDE autocomplete and type checking
2. **Refactoring**: Easy to rename or change values
3. **Consistency**: Single source of truth
4. **Validation**: Built-in `isValid()` methods
5. **Documentation**: Self-documenting code
6. **Error Prevention**: Prevents typos and invalid values

## Best Practices

1. **Always import constants** at the top of your file
2. **Never use hardcoded strings** for status/type values
3. **Use `Rule::in()`** for validation rules
4. **Use `isValid()`** method when validating user input
5. **Use `::ALL`** array when you need all possible values

## Migration from Hardcoded Strings

**Before:**
```php
if ($license->status === 'active') {
    // ...
}
```

**After:**
```php
use App\Constants\LicenseStatus;

if ($license->status === LicenseStatus::ACTIVE) {
    // ...
}
```

