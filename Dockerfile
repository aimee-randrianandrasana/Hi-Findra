FROM composer:2 AS build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache-bookworm

RUN apt-get update -o Acquire::Retries=5

RUN apt-get install -y --no-install-recommends -o Acquire::Retries=5 \
        ca-certificates \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        default-mysql-client \
        unzip

RUN rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo_mysql mbstring gd zip opcache

RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY --from=build /app/vendor /var/www/html/vendor
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/public/uploads

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Limites d'upload PHP (defaut : 2M / 8M, trop bas pour des photos)
RUN printf "upload_max_filesize = 10M\npost_max_size = 12M\n" > /usr/local/etc/php/conf.d/uploads.ini

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
