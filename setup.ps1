# group.one Centralized License Service - Setup Script (Windows)
# This script sets up the Laravel application with Docker/Sail

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "group.one Centralized License Service - Setup" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Check if Docker is installed
try {
    docker --version | Out-Null
    Write-Host "✅ Docker is installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker is not installed. Please install Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Check if .env file exists
if (-Not (Test-Path .env)) {
    Write-Host "📝 Creating .env file from .env.example..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    Write-Host "✅ .env file created" -ForegroundColor Green
} else {
    Write-Host "✅ .env file already exists" -ForegroundColor Green
}

# Install Composer dependencies
if (-Not (Test-Path vendor)) {
    Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Yellow
    docker run --rm `
        -v "${PWD}:/var/www/html" `
        -w /var/www/html `
        laravelsail/php82-composer:latest `
        composer install --ignore-platform-reqs
    Write-Host "✅ Composer dependencies installed" -ForegroundColor Green
} else {
    Write-Host "✅ Composer dependencies already installed" -ForegroundColor Green
}

# Set up Sail alias
Write-Host ""
Write-Host "📝 Setting up Sail alias..." -ForegroundColor Yellow
Write-Host "Add this to your PowerShell profile:" -ForegroundColor Cyan
Write-Host 'function sail { if (Test-Path sail) { sh sail $args } else { sh vendor/bin/sail $args } }' -ForegroundColor White
Write-Host ""

# Start Docker containers
Write-Host "🐳 Starting Docker containers..." -ForegroundColor Yellow
& vendor/bin/sail up -d

# Wait for MySQL to be ready
Write-Host "⏳ Waiting for MySQL to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Generate application key
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
& vendor/bin/sail artisan key:generate

# Run migrations
Write-Host "🗄️  Running database migrations..." -ForegroundColor Yellow
& vendor/bin/sail artisan migrate

# Seed database (optional)
$seed = Read-Host "Do you want to seed the database with sample data? (y/n)"
if ($seed -eq "y" -or $seed -eq "Y") {
    Write-Host "🌱 Seeding database..." -ForegroundColor Yellow
    & vendor/bin/sail artisan db:seed
    Write-Host "✅ Database seeded" -ForegroundColor Green
}

# Generate Swagger documentation
Write-Host "📚 Generating Swagger documentation..." -ForegroundColor Yellow
& vendor/bin/sail artisan l5-swagger:generate

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✅ Setup Complete!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Your application is now running at:" -ForegroundColor Cyan
Write-Host "🌐 Application: http://localhost" -ForegroundColor White
Write-Host "📚 API Documentation: http://localhost/api/documentation" -ForegroundColor White
Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Cyan
Write-Host "  vendor/bin/sail up -d       # Start containers" -ForegroundColor White
Write-Host "  vendor/bin/sail down        # Stop containers" -ForegroundColor White
Write-Host "  vendor/bin/sail artisan     # Run artisan commands" -ForegroundColor White
Write-Host "  vendor/bin/sail composer    # Run composer commands" -ForegroundColor White
Write-Host "  vendor/bin/sail test        # Run tests" -ForegroundColor White
Write-Host ""
Write-Host "Happy coding! 🚀" -ForegroundColor Green

