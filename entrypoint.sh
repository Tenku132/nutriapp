#!/bin/bash

echo "🚀 Booting NutriApp container..."

# Wait for MySQL DB to be available (with fallback check)
echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
attempts=0
until nc -z "$DB_HOST" "$DB_PORT"; do
  attempts=$((attempts+1))
  echo "  → attempt $attempts: still waiting..."
  sleep 2

  if [ $attempts -ge 20 ]; then
    echo "❌ MySQL is still not reachable after 20 attempts. Exiting."
    exit 1
  fi
done

# Run Laravel setup
echo "🔧 Running Laravel setup..."
php artisan migrate --force || true
php artisan config:cache

# Fix file permissions (extra safety)
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true

# Start Apache
echo "📡 Starting Apache..."
exec apache2-foreground
