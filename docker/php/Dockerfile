FROM php:7.4-fpm-alpine

# Erweiterungen für PostgreSQL, Intl und Composer
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev icu-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql intl zip opcache \
    && apk del .build-deps \
    && apk add --no-cache libpq icu-libs libzip git unzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

# Abhängigkeiten gleich ins Image legen. Docker kopiert vendor/ beim ersten
# Start in das leere Volume - der Start braucht dann kein Internet.
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-progress --no-interaction --no-scripts \
    && md5sum composer.lock | cut -d' ' -f1 > vendor/.composer-lock.md5 \
    && mkdir -p var/cache var/log

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
