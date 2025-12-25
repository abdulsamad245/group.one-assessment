# 🚀 Laravel Sail Quick Start Guide

## Prerequisites

- **Docker Desktop** installed and running
- **Windows 10/11** with WSL2 enabled (recommended) OR Docker Desktop for Windows
- **Git** installed

---

## 1. First Time Setup

### Step 1: Start Docker Desktop

Make sure Docker Desktop is running before proceeding.

### Step 2: Start Sail Services

```powershell
# Navigate to project directory
cd Desktop\group.one

# Start all services (MySQL, Redis, Laravel)
.\vendor\bin\sail up -d
```

**What this does:**
- Downloads Docker images (first time only)
- Starts MySQL 8.0 container
- Starts Redis container
- Starts Laravel application container
- Maps port 80 to your application

### Step 3: Run Migrations

```powershell
.\vendor\bin\sail artisan migrate
```

### Step 4: Seed Database

```powershell
.\vendor\bin\sail artisan db:seed
```

### Step 5: Access Application

Open your browser and navigate to:
- **API**: http://localhost/api/v1
- **Swagger Docs**: http://localhost/api/documentation

---

## 2. Daily Usage

### Start Services

```powershell
.\vendor\bin\sail up -d
```

### Stop Services

```powershell
.\vendor\bin\sail down
```

### View Logs

```powershell
# All services
.\vendor\bin\sail logs

# Follow logs (live)
.\vendor\bin\sail logs -f

# Specific service
.\vendor\bin\sail logs laravel.test
```

### Run Artisan Commands

```powershell
# Any artisan command
.\vendor\bin\sail artisan <command>

# Examples:
.\vendor\bin\sail artisan migrate
.\vendor\bin\sail artisan db:seed
.\vendor\bin\sail artisan cache:clear
.\vendor\bin\sail artisan config:clear
.\vendor\bin\sail artisan route:list
```

### Run Tests

```powershell
# All tests
.\vendor\bin\sail test

# Specific test file
.\vendor\bin\sail test tests/Feature/LicenseTest.php

# With coverage
.\vendor\bin\sail test --coverage
```

### Run PHPStan

```powershell
.\vendor\bin\sail composer phpstan
```

### Run Pint (Code Formatting)

```powershell
.\vendor\bin\sail composer pint
```

### Access MySQL Database

```powershell
# MySQL CLI
.\vendor\bin\sail mysql

# Or use a GUI tool with these credentials:
# Host: localhost
# Port: 3306
# Database: license_service
# Username: sail
# Password: password
```

### Access Redis

```powershell
.\vendor\bin\sail redis
```

### Execute Shell Commands in Container

```powershell
# Bash shell
.\vendor\bin\sail shell

# Root shell
.\vendor\bin\sail root-shell

# Run single command
.\vendor\bin\sail exec laravel.test php -v
```

---

## 3. PowerShell Alias (Optional but Recommended)

### Create Alias

Add this to your PowerShell profile:

```powershell
# Open profile
notepad $PROFILE

# Add this line:
function sail { & ".\vendor\bin\sail.bat" @args }

# Save and reload
. $PROFILE
```

### Use Alias

Now you can use `sail` instead of `.\vendor\bin\sail`:

```powershell
sail up -d
sail artisan migrate
sail test
sail down
```

---

## 4. Common Tasks

### Fresh Database

```powershell
# Drop all tables, run migrations, and seed
.\vendor\bin\sail artisan migrate:fresh --seed
```

### Clear All Caches

```powershell
.\vendor\bin\sail artisan optimize:clear
```

### Generate Swagger Documentation

```powershell
.\vendor\bin\sail artisan l5-swagger:generate
```

### Install New Composer Package

```powershell
.\vendor\bin\sail composer require vendor/package
```

### Install New NPM Package (if using frontend)

```powershell
.\vendor\bin\sail npm install package-name
```

---

## 5. Troubleshooting

### Port Already in Use

If port 80 is already in use, edit `.env`:

```env
APP_PORT=8080
```

Then restart:
```powershell
.\vendor\bin\sail down
.\vendor\bin\sail up -d
```

Access at: http://localhost:8080

### Permission Issues

```powershell
# Fix storage permissions
.\vendor\bin\sail artisan storage:link
```

### Database Connection Issues

```powershell
# Check if MySQL is running
.\vendor\bin\sail ps

# Restart services
.\vendor\bin\sail restart mysql
```

### Clear Everything and Start Fresh

```powershell
# Stop and remove all containers and volumes
.\vendor\bin\sail down -v

# Start fresh
.\vendor\bin\sail up -d
.\vendor\bin\sail artisan migrate:fresh --seed
```

---

## 6. Environment Configuration

### Database Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=mysql          # Service name from docker-compose.yml
DB_PORT=3306
DB_DATABASE=license_service
DB_USERNAME=sail
DB_PASSWORD=password
```

### Redis Configuration (.env)

```env
REDIS_HOST=redis       # Service name from docker-compose.yml
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Application URL (.env)

```env
APP_URL=http://localhost
```

---

## 7. Testing API Endpoints

### Using cURL

```powershell
# Get all brands
curl http://localhost/api/v1/brands

# Create a brand
curl -X POST http://localhost/api/v1/brands `
  -H "Content-Type: application/json" `
  -d '{\"name\":\"Test Brand\",\"slug\":\"test-brand\"}'
```

### Using Postman

Import the Postman collection:
- File: `postman/group.one Centralized_License_Service.postman_collection.json`
- Environment: `postman/Local_Environment.postman_environment.json`

---

## 8. Production Deployment

### Build for Production

```powershell
# Optimize autoloader
.\vendor\bin\sail composer install --optimize-autoloader --no-dev

# Cache configuration
.\vendor\bin\sail artisan config:cache
.\vendor\bin\sail artisan route:cache
.\vendor\bin\sail artisan view:cache
```

### Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong passwords
DB_PASSWORD=<strong-password>

# Configure Sentry
SENTRY_LARAVEL_DSN=<your-sentry-dsn>
```

---

## 9. Useful Commands Reference

```powershell
# Service Management
sail up -d              # Start services in background
sail down               # Stop services
sail restart            # Restart services
sail ps                 # List running services

# Artisan
sail artisan <command>  # Run any artisan command
sail artisan tinker     # Laravel REPL

# Composer
sail composer install   # Install dependencies
sail composer update    # Update dependencies
sail composer require   # Add new package

# Testing
sail test               # Run PHPUnit tests
sail test --filter=<name>  # Run specific test

# Database
sail mysql              # MySQL CLI
sail artisan migrate    # Run migrations
sail artisan db:seed    # Run seeders

# Logs
sail logs               # View all logs
sail logs -f            # Follow logs
sail logs laravel.test  # Specific service logs

# Shell Access
sail shell              # Bash shell as sail user
sail root-shell         # Bash shell as root
```

---

**You're all set! Start developing with Laravel Sail.** 🎉


