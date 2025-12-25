# Column Constants Migration Guide

## Overview

This guide explains how to migrate from hardcoded column names to table-specific column constants throughout the codebase.

---

## 1. Column Constant Classes

### Structure

Each table has its own constant class:

```php
// app/Constants/BrandColumns.php
class BrandColumns
{
    public const TABLE = 'brands';
    public const ID = 'id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    // ... all columns
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';
}
```

### Available Classes

- `BrandColumns` - For `brands` table
- `LicenseColumns` - For `licenses` table
- `LicenseKeyColumns` - For `license_keys` table
- `ActivationColumns` - For `activations` table
- `LicenseEventColumns` - For `license_events` table

---

## 2. Migration Files

### Pattern

```php
use App\Constants\LicenseColumns;
use App\Constants\BrandColumns;

Schema::create(LicenseColumns::TABLE, function (Blueprint $table) {
    $table->id();
    $table->foreignId(LicenseColumns::BRAND_ID)
        ->constrained(BrandColumns::TABLE)
        ->onDelete('cascade');
    $table->string(LicenseColumns::CUSTOMER_EMAIL);
    $table->string(LicenseColumns::CUSTOMER_NAME);
    // ... etc
});
```

### Key Points

- Use `::TABLE` for table names
- Use specific column constants for each column
- Use related table constants for foreign key constraints

---

## 3. Model Files

### Fillable Array

```php
use App\Constants\LicenseColumns as Column;

class License extends Model
{
    protected $fillable = [
        Column::BRAND_ID,
        Column::CUSTOMER_EMAIL,
        Column::CUSTOMER_NAME,
        Column::PRODUCT_NAME,
        Column::PRODUCT_SKU,
        Column::LICENSE_TYPE,
        Column::STATUS,
        Column::MAX_ACTIVATIONS,
        Column::CURRENT_ACTIVATIONS,
        Column::EXPIRES_AT,
        Column::METADATA,
    ];
}
```

### Casts Array

```php
protected $casts = [
    Column::EXPIRES_AT => 'datetime',
    Column::METADATA => 'array',
    Column::IS_ACTIVE => 'boolean',
    Column::DELETED_AT => 'datetime',
];
```

### Relationships

```php
public function brand(): BelongsTo
{
    return $this->belongsTo(Brand::class, Column::BRAND_ID);
}
```

### Scopes

```php
public function scopeActive($query)
{
    return $query->where(Column::STATUS, LicenseStatus::ACTIVE->value);
}
```

### Accessors/Mutators

```php
public function getCustomerEmailAttribute($value)
{
    return strtolower($value);
}

// Or with new Laravel 11 syntax
protected function customerEmail(): Attribute
{
    return Attribute::make(
        get: fn ($value) => strtolower($value),
    );
}
```

---

## 4. Repository Files

### Query Methods

```php
use App\Constants\LicenseColumns as Column;

class LicenseRepository
{
    public function findByEmail(string $email): Collection
    {
        return License::where(Column::CUSTOMER_EMAIL, $email)->get();
    }

    public function findActive(): Collection
    {
        return License::where(Column::STATUS, LicenseStatus::ACTIVE->value)
            ->where(Column::EXPIRES_AT, '>', now())
            ->get();
    }

    public function updateStatus(License $license, string $status): License
    {
        $license->update([Column::STATUS => $status]);
        return $license->fresh();
    }
}
```

### Ordering and Filtering

```php
public function paginate(int $perPage = 15)
{
    return License::orderBy(Column::CREATED_AT, 'desc')
        ->paginate($perPage);
}

public function filterByBrand(int $brandId)
{
    return License::where(Column::BRAND_ID, $brandId)->get();
}
```

---

## 5. Service Files

### Creating Records

```php
use App\Constants\LicenseColumns as Column;

public function createLicense(CreateLicenseDTO $dto): License
{
    $license = $this->licenseRepository->create([
        Column::BRAND_ID => $dto->getBrandId(),
        Column::CUSTOMER_EMAIL => $dto->getCustomerEmail(),
        Column::CUSTOMER_NAME => $dto->getCustomerName(),
        Column::PRODUCT_NAME => $dto->getProductName(),
        Column::LICENSE_TYPE => $dto->getLicenseType(),
        Column::STATUS => LicenseStatus::ACTIVE->value,
        Column::MAX_ACTIVATIONS => $dto->getMaxActivations(),
        Column::EXPIRES_AT => $dto->getExpiresAt(),
    ]);

    return $license;
}
```

### Updating Records

```php
public function updateLicense(License $license, array $data): License
{
    $updateData = [];
    
    if (isset($data[Column::CUSTOMER_EMAIL])) {
        $updateData[Column::CUSTOMER_EMAIL] = $data[Column::CUSTOMER_EMAIL];
    }
    
    if (isset($data[Column::STATUS])) {
        $updateData[Column::STATUS] = $data[Column::STATUS];
    }
    
    $license->update($updateData);
    return $license->fresh();
}
```

---

## 6. Factory Files

```php
use App\Constants\LicenseColumns as Column;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;

class LicenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            Column::BRAND_ID => Brand::factory(),
            Column::CUSTOMER_EMAIL => fake()->unique()->safeEmail(),
            Column::CUSTOMER_NAME => fake()->name(),
            Column::PRODUCT_NAME => fake()->words(3, true),
            Column::PRODUCT_SKU => fake()->unique()->bothify('SKU-####-????'),
            Column::LICENSE_TYPE => fake()->randomElement(LicenseType::values()),
            Column::STATUS => LicenseStatus::ACTIVE->value,
            Column::MAX_ACTIVATIONS => fake()->numberBetween(1, 10),
            Column::CURRENT_ACTIVATIONS => 0,
            Column::EXPIRES_AT => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
```

---

## 7. Seeder Files

```php
use App\Constants\LicenseColumns as Column;
use App\Enums\LicenseStatus;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        License::create([
            Column::BRAND_ID => 1,
            Column::CUSTOMER_EMAIL => 'customer@mailinator.com',
            Column::CUSTOMER_NAME => 'John Doe',
            Column::PRODUCT_NAME => 'Premium License',
            Column::LICENSE_TYPE => LicenseType::SUBSCRIPTION->value,
            Column::STATUS => LicenseStatus::ACTIVE->value,
            Column::MAX_ACTIVATIONS => 5,
            Column::EXPIRES_AT => now()->addYear(),
        ]);
    }
}
```

---

## 8. Resource Files

```php
use App\Constants\LicenseColumns as Column;

class LicenseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            Column::ID => $this->{Column::ID},
            Column::CUSTOMER_EMAIL => $this->{Column::CUSTOMER_EMAIL},
            Column::CUSTOMER_NAME => $this->{Column::CUSTOMER_NAME},
            Column::PRODUCT_NAME => $this->{Column::PRODUCT_NAME},
            Column::LICENSE_TYPE => $this->{Column::LICENSE_TYPE},
            Column::STATUS => $this->{Column::STATUS},
            Column::MAX_ACTIVATIONS => $this->{Column::MAX_ACTIVATIONS},
            Column::CURRENT_ACTIVATIONS => $this->{Column::CURRENT_ACTIVATIONS},
            Column::EXPIRES_AT => $this->{Column::EXPIRES_AT},
            Column::CREATED_AT => $this->{Column::CREATED_AT}->toIso8601String(),
            'brand' => new BrandResource($this->whenLoaded('brand')),
        ];
    }
}
```

---

## 9. Benefits

1. **Type Safety** - Typos caught at compile time
2. **Refactoring** - Easy to rename columns across entire codebase
3. **IDE Support** - Autocomplete for column names
4. **Consistency** - Single source of truth
5. **Documentation** - Column constants serve as documentation

---

## 10. Migration Checklist

For each file type:

- [ ] Import the appropriate column constant class
- [ ] Replace all hardcoded column name strings
- [ ] Use `::TABLE` for table names
- [ ] Use specific column constants for each column
- [ ] Test the file after migration
- [ ] Run PHPStan to catch any issues


