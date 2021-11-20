FROM php:8.0-fpm-alpine

RUN apk update \
    && apk add build-base autoconf libsodium libsodium-dev oniguruma oniguruma-dev libzip libzip-dev git

# PHP extensions
RUN pecl install -o -f redis
RUN docker-php-ext-install sodium mbstring bcmath pdo pdo_mysql zip \
    && docker-php-ext-enable sodium mbstring bcmath pdo pdo_mysql zip redis

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --chown=www-data:www-data . /var/www/html
WORKDIR /var/www/html

USER www-data

# production-ready dependencies installation
RUN composer install  --no-interaction --optimize-autoloader --no-dev --prefer-dist --no-scripts
