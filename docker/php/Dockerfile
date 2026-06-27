# syntax=docker/dockerfile:1
# ─────────────────────────────────────────────────────────────────────────────
# Creative Trees Group — PHP 8.5 FPM image (Laravel app, queue, reverb)
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.5-fpm-alpine AS base

# Build-time system dependencies
RUN apk add --no-cache \
        git \
        curl \
        bash \
        zip \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        icu-dev \
        linux-headers \
        $PHPIZE_DEPS

# Core PHP extensions for Laravel + PostgreSQL + Redis
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        intl \
        pcntl \
        exif \
        opcache

# Redis extension (PhpRedis) via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer (copied from official image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Custom PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-creative-trees.ini

WORKDIR /var/www/html

# Run as the same uid as the host user to avoid permission issues on bind mounts
ARG UID=1000
ARG GID=1000
RUN addgroup -g "${GID}" app 2>/dev/null || true \
    && adduser -D -u "${UID}" -G app app 2>/dev/null || true

EXPOSE 9000
CMD ["php-fpm"]
