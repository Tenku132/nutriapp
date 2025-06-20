FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# ✅ Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libonig-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# ✅ Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ✅ Set working directory
WORKDIR /var/www/html

# ✅ Copy all project files
COPY . .

# ✅ Copy .env.example to .env (prevent artisan key error)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# ✅ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# ✅ Laravel setup
RUN php artisan key:generate || true
RUN php artisan config:cache || true

# ✅ Fix permissions for storage and logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ✅ Set Apache DocumentRoot to /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# ✅ Add and prepare entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ✅ Use custom entrypoint at container runtime
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
