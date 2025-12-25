# API Response Pattern Documentation

This document explains the standardized API response pattern implemented across the group.one Centralized License Service.

## Overview

All API endpoints now follow a uniform response structure using:
1. **ApiResponse Trait** - Standardized response methods
2. **Try-Catch Blocks** - Proper error handling in all controllers
3. **Translation Support** - All messages use Laravel's translation system
4. **DTO Pattern** - Request-level DTOs for type-safe data transfer

---

## 1. ApiResponse Trait

Located at: `app/Traits/ApiResponse.php`

### Available Methods

#### Success Response
```php
protected function response(
    int $code = Response::HTTP_OK,
    string $message = '',
    mixed $data = null,
    array $meta = []
): JsonResponse
```

**Response Structure:**
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ... },
    "meta": { ... }
}
```

#### Error Response
```php
protected function error(
    int $code = Response::HTTP_BAD_REQUEST,
    string $message = '',
    mixed $errors = null
): JsonResponse
```

**Response Structure:**
```json
{
    "success": false,
    "message": "Error message",
    "errors": "Error details"
}
```

#### Paginated Response
```php
protected function paginatedResponse(
    mixed $paginator,
    string $message = ''
): JsonResponse
```

**Response Structure:**
```json
{
    "success": true,
    "message": "Records retrieved successfully",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150,
        "from": 1,
        "to": 15
    }
}
```

#### Helper Methods
- `created(string $message, mixed $data)` - HTTP 201
- `noContent(string $message)` - HTTP 204
- `notFound(string $message)` - HTTP 404
- `validationError(string $message, mixed $errors)` - HTTP 422
- `unauthorized(string $message)` - HTTP 401
- `forbidden(string $message)` - HTTP 403

---

## 2. Controller Pattern with Try-Catch

### Example: LicenseController

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class LicenseController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {
            $licenses = $this->licenseRepository->paginate();
            
            return $this->paginatedResponse(
                $licenses,
                __('messages.licenses-found')
            );
        } catch (Exception $e) {
            return $this->error(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                __('messages.server-error'),
                $e->getMessage()
            );
        }
    }

    public function store(StoreLicenseRequest $request): JsonResponse
    {
        try {
            $dto = $request->createLicenseDTO();
            $license = $this->licenseService->createLicense($dto);

            return $this->created(
                __('messages.license-created'),
                new LicenseResource($license->load('brand'))
            );
        } catch (Exception $e) {
            return $this->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                __('messages.error'),
                $e->getMessage()
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $license = $this->licenseRepository->findById($id);

            if (!$license) {
                return $this->notFound(__('messages.license-not-found'));
            }

            return $this->response(
                Response::HTTP_OK,
                __('messages.license-found'),
                new LicenseResource($license)
            );
        } catch (Exception $e) {
            return $this->error(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                __('messages.server-error'),
                $e->getMessage()
            );
        }
    }
}
```

---

## 3. Translation Support

All messages use Laravel's translation system via the `__()` helper.

### Translation File

Located at: `lang/en/messages.php`

```php
return [
    // General
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'not-found' => 'Resource not found',
    'server-error' => 'Internal server error',

    // Licenses
    'license-created' => 'License created successfully',
    'license-updated' => 'License updated successfully',
    'license-found' => 'License retrieved successfully',
    'licenses-found' => 'Licenses retrieved successfully',
    'license-not-found' => 'License not found',
    
    // ... more messages
];
```

### Usage in Controllers

```php
return $this->response(
    Response::HTTP_OK,
    __('messages.license-found'),  // Translated message
    $data
);
```

---

## 4. DTO Pattern

### DTO Structure

Located at: `app/DTOs/`

```php
<?php

namespace App\DTOs;

use App\Traits\DTOToArray;

class CreateLicenseDTO
{
    use DTOToArray;

    private int $brand_id;
    private string $customer_email;
    // ... other properties

    public function setBrandId(int $brand_id): self
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    public function getBrandId(): int
    {
        return $this->brand_id;
    }

    // ... other getters and setters
}
```

### Request Integration

```php
<?php

namespace App\Http\Requests;

use App\DTOs\CreateLicenseDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreLicenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'customer_email' => ['required', 'email', 'max:255'],
            // ... other rules
        ];
    }

    public function createLicenseDTO(): CreateLicenseDTO
    {
        $dto = new CreateLicenseDTO();
        $dto->setBrandId($this->brand_id)
            ->setCustomerEmail($this->customer_email)
            ->setCustomerName($this->customer_name)
            // ... set other properties
            
        return $dto;
    }
}
```

### Controller Usage

```php
public function store(StoreLicenseRequest $request): JsonResponse
{
    try {
        $dto = $request->createLicenseDTO();  // Get DTO from request
        $license = $this->licenseService->createLicense($dto);

        return $this->created(
            __('messages.license-created'),
            new LicenseResource($license)
        );
    } catch (Exception $e) {
        return $this->error(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('messages.error'),
            $e->getMessage()
        );
    }
}
```

---

## Benefits

1. **Consistency**: All API responses follow the same structure
2. **Type Safety**: DTOs provide type-safe data transfer
3. **Error Handling**: Try-catch blocks ensure graceful error handling
4. **Internationalization**: Easy to add multiple languages
5. **Maintainability**: group.one Centralized response logic
6. **Documentation**: Self-documenting code with clear patterns

---

## Files Created/Modified

### New Files
- `app/Traits/ApiResponse.php`
- `app/Traits/DTOToArray.php`
- `app/DTOs/CreateLicenseDTO.php`
- `app/DTOs/UpdateLicenseDTO.php`
- `app/DTOs/CreateActivationDTO.php`
- `lang/en/messages.php`

### Modified Files
- `app/Http/Controllers/Api/V1/LicenseController.php`
- `app/Http/Controllers/Api/V1/ActivationController.php`
- `app/Http/Requests/StoreLicenseRequest.php`
- `app/Http/Requests/UpdateLicenseRequest.php`
- `app/Http/Requests/ActivateLicenseRequest.php`

