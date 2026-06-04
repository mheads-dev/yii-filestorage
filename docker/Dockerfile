FROM composer/composer:latest-bin AS composer

FROM php:8.3-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install opcache
RUN pecl install pcov && docker-php-ext-enable pcov

COPY --from=composer /composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /code

RUN git config --global --add safe.directory /code

#RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-cache
