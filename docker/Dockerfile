# Base image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libmemcached-dev \
    libmagickwand-dev \
    libldap2-dev \
    libc-client-dev \
    libkrb5-dev \
    libonig-dev \
    unzip \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP build options
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl

# Install PHP extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install all required PHP extensions
RUN install-php-extensions \
    pcntl \
    bcmath \
    curl \
    gd \
    igbinary \
    imagick \
    imap \
    intl \
    ldap \
    mbstring \
    memcached \
    msgpack \
    mysqli \
    opcache \
    pcov \
    pdo_mysql \
    readline \
    soap \
    xdebug \
    xml \
    zip

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN docker-php-ext-enable \
    redis \
    igbinary \
    imagick \
    msgpack \
    xdebug \
    pcov \
    memcached

RUN echo "extension=redis" > /usr/local/etc/php/conf.d/redis.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Configure PHP settings
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=200M" > /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "max_execution_time=120" > /usr/local/etc/php/conf.d/execution-time.ini

# Add multiverse repository which contains rar/unrar packages
RUN apt-get update && apt-get install -y unrar-free

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/storage/logs

# Set proper permissions after copying files
RUN chmod -R 777 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN php artisan storage:link

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]