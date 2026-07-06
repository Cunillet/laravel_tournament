FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    linux-headers \
    autoconf \
    build-base

# Configure GD with JPEG/WebP support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

# Install PHP extensions (json, ctype, fileinfo, tokenizer are built-in in PHP 8.4)
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    bcmath \
    mbstring \
    xml \
    exif \
    zip \
    gd

# Install Redis extension from source (for PHP 8.4 compatibility)
RUN git clone --depth 1 --branch 6.2.0 https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/phpredis

# Install OPCache
RUN docker-php-ext-enable opcache

# Configure OPcache
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Create system user for PHP-FPM
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

WORKDIR /var/www

USER www

EXPOSE 9000

CMD ["php-fpm"]
