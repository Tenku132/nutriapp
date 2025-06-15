#!/bin/bash

echo "🚀 Booting NutriApp container..."

# Wait for MySQL DB to be available
echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
for i in {1..20}; do
  nc -z $DB_HOST $DB_PORT && break
  echo "  → still waiting..."
  sleep 2
done

# Run migrations and cache config
echo "🔧 Running Laravel setup..."
php artisan migrate --force
php artisan config:cache

# Start Apache
echo "📡 Starting Apache..."
exec apache2-foreground
