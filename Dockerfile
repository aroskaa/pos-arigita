FROM php:8.3-cli-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    build-base \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    nodejs \
    npm \
    sqlite-dev

RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build production assets with Vite
RUN npm ci && npm run build

# Ensure SQLite file exists and storage permissions
RUN touch /var/www/html/database/database.sqlite && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Default port environment variable for Render
ENV PORT=10000
EXPOSE 10000

# Start Laravel production server
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT"]
