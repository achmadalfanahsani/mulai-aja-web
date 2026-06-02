# ============================================================
# Stage 1: Node.js — Build Vite assets
# ============================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build

# ============================================================
# Stage 2: Composer — Install PHP dependencies
# ============================================================
# Menggunakan php:8.4-cli agar lingkungan PHP sinkron dengan versi produksi
FROM php:8.4-cli-alpine AS composer-builder

# Mengambil sistem binary Composer resmi versi terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
# Menghapus --no-dev agar package dev (seperti Laravel Pail) tidak crash saat discovery
RUN composer install \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist

COPY . .
# Menghapus --no-dev pada pembuatan dump-autoload
RUN composer dump-autoload --optimize --no-scripts

# ============================================================
# Stage 3: Final image — PHP-FPM + Nginx + MySQL
# ============================================================
FROM php:8.4-fpm-alpine AS final

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    mariadb-connector-c-dev \
    supervisor \
    curl \
    unzip \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache

# Configure PHP for production
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor (manages nginx + php-fpm)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copy application files
COPY --from=composer-builder /app /var/www/html
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Setup storage directory
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    database \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache database

# Copy entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy .env file
COPY .env.example /var/www/html/.env

# Cloud Run uses PORT env variable (default 8080)
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]