# PHP image for Lumen with Composer and necessary extensions
FROM php:8.2-fpm

# dependencies 
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql

# Composer installation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy the application code 
WORKDIR /var/www/html
COPY . .

# necesary folders and permissions
RUN mkdir -p /var/www/html/bootstrap/cache /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel dependencies
RUN composer install --no-dev --optimize-autoloader


# Exponer puerto 9000 para PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
