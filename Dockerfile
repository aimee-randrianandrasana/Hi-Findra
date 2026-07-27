FROM composer:2 AS build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring gd zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY --from=build /app/vendor /var/www/html/vendor
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/public/uploads
