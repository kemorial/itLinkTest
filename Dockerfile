FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction

FROM php:8.4-fpm

WORKDIR /var/www/app

COPY --from=composer /app/vendor ./vendor
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    && docker-php-ext-enable pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/* 

COPY --chmod=755 entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh

COPY . .

CMD ["/entrypoint.sh"]
