# PHP image for Lumen with Composer and necessary extensions
FROM php:8.2-apache

# UID & GID
ARG UID=1000
ARG GID=1000


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

# modify user and group  www-data
RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -g ${GID} www-data && \
    chown -R www-data:www-data /var/www/html

# necesary folders and permissions
RUN mkdir -p /var/www/html/bootstrap/cache /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Apache rewrite module
RUN a2enmod rewrite

# Apache virtual Host
COPY ./apache/laravel.conf /etc/apache2/sites-available/000-default.conf


# Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]

