# group.one Centralized License Service - Project Summary

## Overview

This is a comprehensive **multi-tenant, multi-brand license management system** built with Laravel 11. The system supports various license types, seat-based activations, and provides a complete RESTful API for managing licenses across multiple brands.

## Project Structure

```
license-service/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckExpiredLicensesCommand.php
│   ├── Constants/
│   │   ├── ActivationStatus.php
│   │   ├── LicenseEventType.php
│   │   ├── LicenseKeyStatus.php
│   │   ├── LicenseStatus.php
│   │   └── LicenseType.php
│   ├── DTOs/
│   │   ├── ActivationDTO.php
│   │   ├── BrandDTO.php
│   │   └── LicenseDTO.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/
│   │   │   │   ├── ActivationController.php
│   │   │   │   ├── BrandController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── LicenseController.php
│   │   │   │   └── LicenseKeyController.php
│   │   │   └── Controller.php
│   │   ├── Requests/
│   │   │   ├── ActivateRequest.php
│   │   │   ├── CreateBrandRequest.php
│   │   │   ├── CreateLicenseRequest.php
│   │   │   ├── DeactivateRequest.php
│   │   │   ├── GenerateLicenseKeyRequest.php
│   │   │   └── UpdateBrandRequest.php
│   │   └── Resources/
│   │       ├── ActivationResource.php
│   │       ├── BrandResource.php
│   │       ├── LicenseKeyResource.php
│   │       └── LicenseResource.php
│   ├── Jobs/
│   │   ├── CheckExpiredLicensesJob.php
│   │   ├── ProcessLicenseProvisioningJob.php
│   │   └── SendActivationNotificationJob.php
│   ├── Models/
│   │   ├── Activation.php
│   │   ├── Brand.php
│   │   ├── License.php
│   │   ├── LicenseEvent.php
│   │   └── LicenseKey.php
│   ├── Repositories/
│   │   ├── ActivationRepository.php
│   │   ├── BrandRepository.php
│   │   ├── LicenseKeyRepository.php
│   │   └── LicenseRepository.php
│   └── Services/
│       ├── ActivationService.php
│       ├── CustomerService.php
│       ├── LicenseEventService.php
│       └── LicenseService.php
├── bootstrap/
│   └── app.php
├── config/
│   └── l5-swagger.php
├── database/
│   ├── factories/
│   │   ├── ActivationFactory.php
│   │   ├── BrandFactory.php
│   │   ├── LicenseFactory.php
│   │   └── LicenseKeyFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_brands_table.php
│   │   ├── 2024_01_01_000002_create_licenses_table.php
│   │   ├── 2024_01_01_000003_create_license_keys_table.php
│   │   ├── 2024_01_01_000004_create_activations_table.php
│   │   └── 2024_01_01_000005_create_license_events_table.php
│   └── seeders/
│       ├── ActivationSeeder.php
│       ├── BrandSeeder.php
│       ├── DatabaseSeeder.php
│       ├── LicenseKeySeeder.php
│       └── LicenseSeeder.php
├── docker/
├── public/
│   └── index.php
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── ActivationApiTest.php
│   │   ├── BrandApiTest.php
│   │   └── LicenseApiTest.php
│   ├── Unit/
│   │   ├── ActivationServiceTest.php
│   │   └── LicenseServiceTest.php
│   └── TestCase.php
├── .github/
│   └── workflows/
│       └── ci.yml
├── .env.example
├── .gitignore
├── artisan
├── CHANGELOG.md
├── composer.json
├── CONTRIBUTING.md
├── docker-compose.yml
├── phpstan.neon
├── phpstan-rules.php
├── phpunit.xml
├── pint.json
├── postman/
│   ├── group.one Centralized_License_Service.postman_collection.json
│   ├── Local_Environment.postman_environment.json
│   └── README.md
├── README.md
├── setup.ps1
└── setup.sh
```

## Key Features

### 1. Multi-Brand Management (US1)
- Create, read, update, delete brands
- Each brand is a separate tenant
- Brand-specific settings and configurations

### 2. License Creation (US2)
- Support for multiple license types: Perpetual, Subscription, Trial
- Configurable expiration dates
- Product-specific licensing
- Customer association

### 3. License Key Generation (US3)
- Unique license key generation
- Format: XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX
- Key validation and status tracking

### 4. Activation/Deactivation (US4)
- Seat-based activation management
- Device tracking (identifier, name, IP, user agent)
- Maximum activation limits
- Deactivation support

### 5. Seat Management (US5)
- Track current vs. maximum activations
- Prevent over-activation
- Real-time activation status

### 6. Customer Lookup (US6)
- Cross-brand customer license lookup
- Aggregated license statistics
- Email-based search

## Technology Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis
- **Container**: Docker with Laravel Sail
- **Testing**: PHPUnit
- **Static Analysis**: PHPStan Level 5 + Larastan
- **Code Style**: Laravel Pint (PSR-12)
- **API Documentation**: Swagger/OpenAPI 3.0
- **CI/CD**: GitHub Actions

## Architecture Patterns

1. **Repository Pattern** - Data access abstraction
2. **Service Layer** - Business logic encapsulation
3. **DTO Pattern** - Data transfer between layers
4. **Resource Pattern** - API response formatting
5. **Request Validation** - Form request classes
6. **Queue Jobs** - Async processing
7. **Event Logging** - Audit trail

## Getting Started

### Quick Start (Recommended)

**Windows:**
```powershell
.\setup.ps1
```

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

### Manual Setup

See [README.md](README.md) for detailed installation instructions.

## API Documentation

Once running, access Swagger UI at:
```
http://localhost/api/documentation
```

## Testing

```bash
# Run all tests
sail artisan test

# Run with coverage
sail artisan test --coverage

# Run specific test suite
sail artisan test --testsuite=Feature
sail artisan test --testsuite=Unit
```

## Code Quality

```bash
# Static analysis
sail composer phpstan

# Code formatting check
sail composer pint:test

# Fix code formatting
sail composer pint
```

## CI/CD Pipeline

The GitHub Actions pipeline includes:
- ✅ PHP CS Fixer linting
- ✅ PHPStan static analysis
- ✅ Full test suite with coverage
- ✅ Diff coverage check (≥50%)
- ✅ PR description validation
- ✅ Auto-deploy to staging (develop branch)
- ✅ Auto-deploy to production (trunk branch)

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development workflow and standards.

## License

MIT License

