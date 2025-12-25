#!/bin/bash

# group.one Centralized License Service - Setup Script
# This script sets up the Laravel application with Docker/Sail

set -e

echo "=========================================="
echo "group.one Centralized License Service - Setup"
echo "=========================================="
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

echo "✅ Docker is installed"

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✅ Docker Compose is installed"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Install Composer dependencies
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    echo "✅ Composer dependencies installed"
else
    echo "✅ Composer dependencies already installed"
fi

# Set up Sail alias
echo ""
echo "📝 Setting up Sail alias..."
echo "Add this to your ~/.bashrc or ~/.zshrc:"
echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'"
echo ""

# Start Docker containers
echo "🐳 Starting Docker containers..."
./vendor/bin/sail up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Generate application key
echo "🔑 Generating application key..."
./vendor/bin/sail artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
./vendor/bin/sail artisan migrate

# Seed database (optional)
read -p "Do you want to seed the database with sample data? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🌱 Seeding database..."
    ./vendor/bin/sail artisan db:seed
    echo "✅ Database seeded"
fi

# Generate Swagger documentation
echo "📚 Generating Swagger documentation..."
./vendor/bin/sail artisan l5-swagger:generate

echo ""
echo "=========================================="
echo "✅ Setup Complete!"
echo "=========================================="
echo ""
echo "Your application is now running at:"
echo "🌐 Application: http://localhost"
echo "📚 API Documentation: http://localhost/api/documentation"
echo ""
echo "Useful commands:"
echo "  ./vendor/bin/sail up -d       # Start containers"
echo "  ./vendor/bin/sail down        # Stop containers"
echo "  ./vendor/bin/sail artisan     # Run artisan commands"
echo "  ./vendor/bin/sail composer    # Run composer commands"
echo "  ./vendor/bin/sail test        # Run tests"
echo ""
echo "Happy coding! 🚀"

