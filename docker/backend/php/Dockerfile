# syntax=docker/dockerfile:1

FROM php:8.5.5-fpm AS build
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y zip unzip libpq-dev

RUN docker-php-ext-install pdo_pgsql
RUN pecl install redis && docker-php-ext-enable redis

ARG UID=1000
ARG GID=1000

RUN usermod -u ${UID} -s /bin/bash www-data && groupmod -g ${GID} www-data

RUN chown www-data:www-data /var/www
USER www-data

COPY --chown=www-data:www-data ./backend/composer.json ./
COPY --chown=www-data:www-data ./backend/composer.lock ./

COPY --chown=www-data:www-data --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-scripts --no-autoloader

COPY --chown=www-data:www-data ./backend .

RUN composer dump-autoload

COPY --chown=www-data:www-data backend/package.json ./
