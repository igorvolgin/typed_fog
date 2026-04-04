FROM php:8.5.4-cli-alpine AS base

RUN apk add --no-cache zip unzip $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pcntl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

EXPOSE 8000


FROM base AS dev

COPY composer.json composer.lock ./
RUN composer install --no-scripts

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


FROM base AS prod

COPY . .
RUN composer install \
    && chown -R www-data:www-data storage bootstrap/cache

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
