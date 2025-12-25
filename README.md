# group.one Centralized License Service

A comprehensive multi-tenant, multi-brand license management system built with Laravel 11.

## Features

- ✅ Multi-brand license management
- ✅ Multiple license types (Perpetual, Subscription, Trial)
- ✅ Seat-based activation management
- ✅ Customer license lookup across brands
- ✅ RESTful API with versioning (v1)
- ✅ Complete Swagger/OpenAPI documentation
- ✅ Queue-based async processing
- ✅ Comprehensive logging
- ✅ PHPStan Level 5+ static analysis
- ✅ Full test coverage
- ✅ CI/CD with GitHub Actions
- ✅ Docker support with Laravel Sail

## Requirements

- PHP 8.2+
- Composer
- Docker & Docker Compose (for Sail)
- MySQL 8.0+ or PostgreSQL
- Redis

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd license-service
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure environment variables

Edit `.env` and set your database and Redis credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=license_service
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
QUEUE_CONNECTION=redis
```

### 5. Start Docker containers (Laravel Sail)

```bash
# Set up Sail alias (optional but recommended)
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

# Start containers
sail up -d
```

### 6. Run migrations

```bash
sail artisan migrate
```

### 7. (Optional) Seed database

```bash
sail artisan db:seed
```

## API Documentation

Once the application is running, access the Swagger UI at:

```
http://localhost/api/documentation
```

### Postman Collection

A complete Postman collection is available in the `postman/` directory:

- **Collection**: `postman/group.one Centralized_License_Service.postman_collection.json`
- **Environment**: `postman/Local_Environment.postman_environment.json`

Import both files into Postman to start testing the API immediately. See [postman/README.md](postman/README.md) for detailed instructions.

## API Endpoints

### Brands
- `GET /api/v1/brands` - List all brands
- `POST /api/v1/brands` - Create a brand
- `GET /api/v1/brands/{id}` - Get brand details
- `PUT /api/v1/brands/{id}` - Update brand
- `DELETE /api/v1/brands/{id}` - Delete brand

### Licenses
- `GET /api/v1/licenses` - List all licenses
- `POST /api/v1/licenses` - Create a license
- `GET /api/v1/licenses/{id}` - Get license details
- `PUT /api/v1/licenses/{id}` - Update license

### License Keys
- `GET /api/v1/license-keys` - List all license keys
- `POST /api/v1/license-keys` - Generate a license key
- `GET /api/v1/license-keys/{id}` - Get license key details

### Activations
- `POST /api/v1/activations` - Activate a license
- `POST /api/v1/deactivations` - Deactivate a license
- `GET /api/v1/activations/status` - Check activation status

### Customers
- `GET /api/v1/customers/licenses?email={email}` - Get all licenses for a customer

## Running Tests

```bash
# Run all tests
sail artisan test

# Run with coverage
sail artisan test --coverage

# Run specific test file
sail artisan test tests/Feature/BrandApiTest.php
```

## Code Quality

### PHPStan (Static Analysis)

```bash
sail composer phpstan
```

### Code Formatting (Laravel Pint)

```bash
# Check code style
sail composer pint:test

# Fix code style
sail composer pint
```

## Queue Workers

Start the queue worker to process async jobs:

```bash
sail artisan queue:work
```

## Development Workflow

### Branch Strategy

- `trunk` - Production branch
- `develop` - Main development branch
- Feature branches: `feature/issue-number-description`
- Bug fixes: `fix/issue-number-description`
- Enhancements: `enhancement/issue-number-description`
- Chores: `chore/issue-number-description`

### Commit Conventions

Use imperative mood: "This commit will..."

```
Add license activation endpoint
Fix validation error in brand creation
Update README with installation instructions
```

### Pull Request Process

1. Create feature branch from `develop`
2. Make changes with small, frequent commits
3. Write/update tests
4. Ensure all tests pass
5. Run PHPStan and fix any issues
6. Create PR with detailed description
7. Wait for CI checks to pass
8. Get at least 1 approval
9. Squash and merge

## CI/CD Pipeline

The GitHub Actions pipeline runs on every PR and push:

1. **Linting** - PHP CS Fixer checks
2. **Static Analysis** - PHPStan level 5
3. **Tests** - Full test suite with coverage
4. **Diff Coverage** - Ensures new code has ≥50% coverage
5. **Deploy** - Auto-deploy to staging (develop) or production (trunk)

## Architecture

### Layers

1. **Controllers** - Handle HTTP requests/responses
2. **Requests** - Validate incoming data
3. **Resources** - Format API responses
4. **Services** - Business logic
5. **Repositories** - Data access layer
6. **Models** - Eloquent ORM models
7. **DTOs** - Data transfer objects
8. **Jobs** - Async queue processing

### Database Schema

- `brands` - Brand/tenant information
- `licenses` - License records
- `license_keys` - Generated license keys
- `activations` - Device activations
- `license_events` - Audit log

## License

MIT License

## Support

For support, email support@licenseservice.com

# group.one-assessment
