# StudHub dev PHP image.
# Production image will live elsewhere (Week 11 hardening).
FROM php:8.3-fpm

ARG WWWUSER=1000
ARG WWWGROUP=1000

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        curl \
        ca-certificates \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        intl \
        mbstring \
        pcntl \
        gd \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Match host UID/GID so volume-mounted files are owned by the dev user.
RUN groupadd --force -g $WWWGROUP studhub \
    && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER studhub

WORKDIR /var/www/html
USER studhub
