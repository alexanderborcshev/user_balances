FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng libpng-dev \
    libjpeg-turbo libjpeg-turbo-dev \
    libwebp libwebp-dev \
    freetype freetype-dev \
    oniguruma oniguruma-dev \
    icu icu-dev \
    libzip libzip-dev \
    zip unzip \
    bash \
    shadow \
    postgresql-client postgresql-dev build-base

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-webp \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        intl \
        zip \
        opcache

# Configure PHP FPM
COPY php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Create working directory
WORKDIR /var/www/html

# Fix www-data to match common host uid/gid 1000 for permissions
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data || true

USER www-data
