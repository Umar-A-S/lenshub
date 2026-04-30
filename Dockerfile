# STAGE 1: Node.js untuk Build Asset (Vite)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# STAGE 2: Composer untuk Production Dependencies
FROM composer:2.7 AS composer-builder
WORKDIR /app
COPY composer*.json ./
# Kita install tanpa dev-dependencies untuk mengecilkan ukuran
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# STAGE 3: Final Image (PHP 8.3-FPM Alpine)
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install System Extensions & PHP Extensions
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    oniguruma-dev \
    icu-dev \
    linux-headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl opcache

# Copy konfigurasi Opcache khusus produksi
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Buat user non-root 'www'
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Copy aplikasi (root access sementara untuk permission)
COPY --chown=www:www . .
COPY --from=composer-builder --chown=www:www /app/vendor ./vendor
COPY --from=node-builder --chown=www:www /app/public/build ./public/build

# Atur permission folder kritikal
RUN chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Pindah ke user non-root untuk keamanan
USER www

EXPOSE 9000
CMD ["php-fpm"]