# Developer Quick Reference Guide

## Quick Patterns for Common Tasks

---

## 1. Creating a New Controller Method

### Pattern to Follow

```php
use App\Traits\ApiResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class YourController extends Controller
{
    use ApiResponse;

    public function yourMethod(YourRequest $request): JsonResponse
    {
        try {
            // Your business logic here
            $result = $this->service->doSomething($request->validated());
            
            // Return success response with translation
            return $this->response(
                Response::HTTP_OK, 
                __('messages.your-success-message'), 
                new YourResource($result)
            );
        } catch (Exception $e) {
            return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
```

### Key Rules

1. ✅ Always use `ApiResponse` trait
2. ✅ Always wrap in try-catch
3. ✅ Always use dedicated Request class for validation
4. ✅ Always use `__('messages.xxx')` for messages
5. ✅ Always use `Response::HTTP_XXX` constants
6. ✅ Always return `JsonResponse`

---

## 2. Creating a New Request Class

### Pattern to Follow

```php
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class YourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or your authorization logic
    }

    public function rules(): array
    {
        return [
            'field_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'status' => ['required', Rule::in(YourEnum::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'field_name.required' => __('messages.field-required'),
            'email.email' => __('messages.email-invalid'),
        ];
    }
}
```

### Key Rules

1. ✅ All validation rules in `rules()` method
2. ✅ All custom messages use `__('messages.xxx')`
3. ✅ Use `Rule::in(Enum::values())` for enum validation
4. ✅ No validation in controllers

---

## 3. Using Column Constants

### In Migrations

```php
use App\Constants\YourTableConstant;

Schema::create(YourTableConstant::TABLE, function (Blueprint $table) {
    $table->id();
    $table->string(YourTableConstant::NAME);
    $table->string(YourTableConstant::EMAIL)->unique();
    $table->timestamps();
});
```

### In Models

```php
use App\Constants\YourTableConstant as Column;

class YourModel extends Model
{
    protected $fillable = [
        Column::NAME,
        Column::EMAIL,
        Column::STATUS,
    ];

    protected $casts = [
        Column::IS_ACTIVE => 'boolean',
        Column::CREATED_AT => 'datetime',
    ];
}
```

### In Repositories

```php
use App\Constants\YourTableConstant as Column;

public function findByEmail(string $email)
{
    return YourModel::where(Column::EMAIL, $email)->first();
}

public function getActive()
{
    return YourModel::where(Column::STATUS, YourEnum::ACTIVE->value)->get();
}
```

### In Services

```php
use App\Constants\YourTableConstant as Column;

public function create(array $data)
{
    return $this->repository->create([
        Column::NAME => $data['name'],
        Column::EMAIL => $data['email'],
        Column::STATUS => YourEnum::ACTIVE->value,
    ]);
}
```

### Key Rules

1. ✅ Always use `YourTableConstant::COLUMN_NAME`
2. ✅ Never hardcode column names as strings
3. ✅ Use `as Column` alias for cleaner code
4. ✅ Use `::TABLE` for table names

---

## 4. Using Enums

### In Migrations

```php
use App\Enums\YourEnum;

$table->enum(YourTableColumns::STATUS, YourEnum::values())
    ->default(YourEnum::ACTIVE->value);
```

### In Services

```php
use App\Enums\YourEnum;

// Set enum value
$data[Column::STATUS] = YourEnum::ACTIVE->value;

// Check enum value
if ($model->status === YourEnum::ACTIVE->value) {
    // Do something
}
```

### In Validation

```php
use App\Enums\YourEnum;
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'status' => ['required', Rule::in(YourEnum::values())],
    ];
}
```

### Key Rules

1. ✅ Always use `YourEnum::CASE->value` to get string value
2. ✅ Use `YourEnum::values()` for validation
3. ✅ Never hardcode enum values as strings

---

## 5. Adding Translation Messages

### In `lang/en/messages.php`

```php
return [
    // Your category
    'your-resource-found' => 'Your resource retrieved successfully',
    'your-resource-created' => 'Your resource created successfully',
    'your-resource-updated' => 'Your resource updated successfully',
    'your-resource-deleted' => 'Your resource deleted successfully',
    'your-resource-not-found' => 'Your resource not found',
];
```

### Usage in Controllers

```php
return $this->response(Response::HTTP_OK, __('messages.your-resource-found'), $data);
return $this->created(__('messages.your-resource-created'), $data);
return $this->error(Response::HTTP_NOT_FOUND, __('messages.your-resource-not-found'));
```

### Key Rules

1. ✅ Use kebab-case for message keys
2. ✅ Group messages by category
3. ✅ Always use `__('messages.xxx')` in code
4. ✅ Never hardcode messages

---

## 6. API Response Methods

### Available Methods from ApiResponse Trait

```php
// Success response (200)
$this->response(Response::HTTP_OK, 'Message', $data);

// Created response (201)
$this->created('Message', $data);

// Paginated response (200)
$this->paginatedResponse($paginatedData, 'Message');

// Error response (4xx/5xx)
$this->error(Response::HTTP_NOT_FOUND, 'Error message');
$this->error(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation error');
```

### Response Structure

All responses follow this structure:

```json
{
    "success": true,
    "message": "Your message here",
    "data": { ... }
}
```

Or for errors:

```json
{
    "success": false,
    "message": "Error message here"
}
```

---

## 7. Common HTTP Status Codes

```php
use Symfony\Component\HttpFoundation\Response;

Response::HTTP_OK                    // 200 - Success
Response::HTTP_CREATED               // 201 - Resource created
Response::HTTP_NO_CONTENT            // 204 - Success with no content
Response::HTTP_BAD_REQUEST           // 400 - Bad request
Response::HTTP_UNAUTHORIZED          // 401 - Not authenticated
Response::HTTP_FORBIDDEN             // 403 - Not authorized
Response::HTTP_NOT_FOUND             // 404 - Resource not found
Response::HTTP_UNPROCESSABLE_ENTITY  // 422 - Validation error
Response::HTTP_INTERNAL_SERVER_ERROR // 500 - Server error
```

---

## 8. Checklist for New Features

When adding a new feature:

- [ ] Create Request class with all validation
- [ ] Add translation messages to `lang/en/messages.php`
- [ ] Create column constants class if new table
- [ ] Create enums if new status/type fields
- [ ] Use try-catch in all controller methods
- [ ] Use ApiResponse trait for all responses
- [ ] Use `__('messages.xxx')` for all messages
- [ ] Use column constants everywhere
- [ ] Use enum `->value` for enum values
- [ ] Write tests for the feature
- [ ] Update API documentation

---

## 9. File Naming Conventions

```
Controllers:     YourResourceController.php
Requests:        StoreYourResourceRequest.php, UpdateYourResourceRequest.php
Resources:       YourResourceResource.php
Models:          YourResource.php
Repositories:    YourResourceRepository.php
Services:        YourResourceService.php
Constants:       YourResourceConstant.php (named after table, e.g., BrandConstant, LicenseConstant)
Enums:           YourResourceStatus.php, YourResourceType.php
Migrations:      YYYY_MM_DD_HHMMSS_create_your_resources_table.php
```

---

## 10. Common Mistakes to Avoid

❌ **DON'T:**
- Hardcode column names: `'customer_email'`
- Hardcode enum values: `'active'`
- Hardcode messages: `'Brand not found'`
- Put validation in controllers
- Use `response()->json()` directly
- Skip try-catch blocks
- Use inline imports

✅ **DO:**
- Use constants: `BrandConstant::CUSTOMER_EMAIL`
- Use enums: `LicenseStatus::ACTIVE->value`
- Use translations: `__('messages.brand-not-found')`
- Put validation in Request classes
- Use ApiResponse trait methods
- Wrap all methods in try-catch
- Put all imports at the top of the file

---

## Need Help?

Refer to these guides:
- `COLUMN_CONSTANTS_MIGRATION_GUIDE.md` - For column constants usage
- `ENUMS_AND_CONSTANTS_GUIDE.md` - For enum usage
- `SENTRY_SETUP_GUIDE.md` - For Sentry configuration
- `COMPLETED_IMPLEMENTATION.md` - For implementation summary


