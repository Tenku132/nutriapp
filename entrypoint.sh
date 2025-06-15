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

# Ensure .env file exists
if [ ! -f .env ]; then
  echo "⚙️  No .env file found. Copying from .env.example..."
  cp .env.example .env
fi

# Run Laravel setup
echo "🔧 Running Laravel setup..."

# Generate key only if APP_KEY is not set
if grep -q "APP_KEY=base64" .env; then
  echo "✅ APP_KEY already set. Skipping key:generate."
else
  php artisan key:generate || echo "⚠️ key:generate failed"
fi

php artisan migrate --force || echo "⚠️ migrate failed"
php artisan config:cache || echo "⚠️ config:cache failed"

# Fix file permissions (extra safety)
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true

# Start Apache
echo "📡 Starting Apache..."
exec apache2-foreground
