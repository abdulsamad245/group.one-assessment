# Quick Reference Guide

## Setup Commands

### First Time Setup
```bash
# Windows
.\setup.ps1

# Linux/Mac
chmod +x setup.sh
./setup.sh
```

### Manual Setup
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Start Docker containers
./vendor/bin/sail up -d

# Generate app key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database (optional)
./vendor/bin/sail artisan db:seed

# Generate Swagger docs
./vendor/bin/sail artisan l5-swagger:generate
```

## Daily Development

```bash
# Start containers
sail up -d

# Stop containers
sail down

# View logs
sail logs

# Access MySQL
sail mysql

# Access Redis CLI
sail redis

# Clear cache
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
```

## Testing

```bash
# Run all tests
sail artisan test

# Run with coverage
sail artisan test --coverage

# Run specific test file
sail artisan test tests/Feature/BrandApiTest.php

# Run specific test method
sail artisan test --filter test_can_create_brand

# Run feature tests only
sail artisan test --testsuite=Feature

# Run unit tests only
sail artisan test --testsuite=Unit
```

## Code Quality

```bash
# Run PHPStan
sail composer phpstan

# Check code style
sail composer pint:test

# Fix code style
sail composer pint

# Run all quality checks
sail composer phpstan && sail composer pint:test && sail artisan test
```

## Database

```bash
# Run migrations
sail artisan migrate

# Rollback last migration
sail artisan migrate:rollback

# Reset database
sail artisan migrate:fresh

# Reset and seed
sail artisan migrate:fresh --seed

# Create new migration
sail artisan make:migration create_table_name

# Create seeder
sail artisan make:seeder TableSeeder
```

## API Endpoints

### Brands
```bash
# List brands
GET /api/v1/brands

# Create brand
POST /api/v1/brands
{
  "name": "Brand Name",
  "slug": "brand-slug",
  "contact_email": "contact@brand.com"
}

# Get brand
GET /api/v1/brands/{id}

# Update brand
PUT /api/v1/brands/{id}

# Delete brand
DELETE /api/v1/brands/{id}
```

### Licenses
```bash
# List licenses
GET /api/v1/licenses

# Create license
POST /api/v1/licenses
{
  "brand_id": 1,
  "customer_email": "customer@mailinator.com",
  "customer_name": "John Doe",
  "product_name": "Premium Software",
  "product_sku": "PREM-001",
  "license_type": "subscription",
  "max_activations": 5,
  "expires_at": "2025-12-31"
}

# Get license
GET /api/v1/licenses/{id}

# Update license
PUT /api/v1/licenses/{id}
```

### License Keys
```bash
# Generate license key
POST /api/v1/license-keys
{
  "license_id": 1
}

# Get license key
GET /api/v1/license-keys/{id}
```

### Activations
```bash
# Activate license
POST /api/v1/activations
{
  "license_key": "XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX",
  "device_identifier": "DEVICE-123",
  "device_name": "John's Laptop"
}

# Deactivate license
POST /api/v1/deactivations
{
  "activation_id": 1
}

# Check activation status
GET /api/v1/activations/status?license_key=XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX
```

### Customer Lookup
```bash
# Get customer licenses
GET /api/v1/customers/licenses?email=customer@mailinator.com
```

## Artisan Commands

```bash
# Check expired licenses
sail artisan licenses:check-expired

# Queue worker
sail artisan queue:work

# List routes
sail artisan route:list

# List routes for API v1
sail artisan route:list --path=api/v1

# Generate Swagger docs
sail artisan l5-swagger:generate

# Create controller
sail artisan make:controller Api/V1/ControllerName

# Create model with migration and factory
sail artisan make:model ModelName -mf

# Create request
sail artisan make:request RequestName

# Create resource
sail artisan make:resource ResourceName

# Create job
sail artisan make:job JobName

# Create test
sail artisan make:test TestName
sail artisan make:test TestName --unit
```

## Git Workflow

```bash
# Create feature branch
git checkout develop
git pull origin develop
git checkout -b feature/123-description

# Make changes and commit
git add .
git commit -m "Add feature description"

# Push to remote
git push origin feature/123-description

# After PR is merged, clean up
git checkout develop
git pull origin develop
git branch -d feature/123-description
```

## Troubleshooting

```bash
# Rebuild containers
sail down
sail build --no-cache
sail up -d

# Clear all caches
sail artisan optimize:clear

# Reset permissions
sudo chown -R $USER:$USER .

# View container logs
sail logs -f

# Access container shell
sail shell

# Run composer update
sail composer update

# Dump autoload
sail composer dump-autoload
```

## Environment Variables

Key variables in `.env`:
```env
APP_NAME="group.one Centralized License Service"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=license_service
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
QUEUE_CONNECTION=redis

L5_SWAGGER_GENERATE_ALWAYS=true
```

## Useful URLs

- Application: http://localhost
- API Documentation: http://localhost/api/documentation
- API Base: http://localhost/api/v1

## Performance

```bash
# Cache config
sail artisan config:cache

# Cache routes
sail artisan route:cache

# Optimize
sail artisan optimize

# Clear optimizations
sail artisan optimize:clear
```

