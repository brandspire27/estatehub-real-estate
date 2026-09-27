FROM php:8.3-cli-bookworm AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libcurl4-openssl-dev \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install -j"$(nproc)" bcmath curl dom fileinfo intl mbstring opcache pdo_mysql xml zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

FROM node:22-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=app /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

FROM app AS runtime

COPY --from=assets /app/public/build ./public/build

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DEMO_READ_ONLY=true

EXPOSE 8080

USER www-data

CMD ["sh", "railway/start.sh"]