# docker/php/Dockerfile
FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpng-dev libxml2-dev zip libicu-dev \
    libpq-dev libmcrypt-dev libpng-dev default-mysql-client

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath
RUN docker-php-ext-install intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# install npm (needed? build handled separately)
# set permissions
RUN useradd -m -u 1000 laraveluser