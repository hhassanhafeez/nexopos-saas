FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev \
    libzip-dev libgd-dev libicu-dev \
    libcurl4-openssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions (tokenizer, ctype, fileinfo, curl are bundled in the base image)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath \
        gd intl zip opcache xml dom

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Non-root user (matches host uid 1000 typically)
RUN groupmod -g 1000 www-data && usermod -u 1000 -g www-data www-data

# Ensure storage and cache are writable by www-data at startup
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
