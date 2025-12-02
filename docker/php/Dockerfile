# -----------------------------------------
# Stage 1: Build Frontend (Node)
# -----------------------------------------
FROM node:20-alpine AS frontend-build
WORKDIR /var/www/client

COPY client/package*.json ./
RUN npm ci

COPY client/ .
# Create server directory for vite output
RUN mkdir -p ../server/public
RUN npm run build

# -----------------------------------------
# Stage 2: PHP Application
# -----------------------------------------
FROM php:8.2-fpm

WORKDIR /var/www/server


RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && curl -o /usr/local/bin/php-fpm-healthcheck https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck \
    && rm -rf /var/lib/apt/lists/*

COPY server/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer

# Copy application source
COPY server/ .

# Copy frontend build output from Node stage
COPY --from=frontend-build /var/www/server/public/build ./public/build

# Ensure storage and cache directories exist and are writable
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/server \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
