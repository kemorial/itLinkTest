FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction

FROM php:8.4-fpm

WORKDIR /var/www/app

COPY --from=composer /app/vendor .
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    && docker-php-ext-enable pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/* 

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

COPY . .

ENTRYPOINT ["/entrypoint.sh"]