FROM php:7.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

# Install Redis extension
RUN pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy CakePHP application
COPY cake2 /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/app/tmp \
    && chmod -R 775 /var/www/html/app/tmp

# Configure Apache to point to CakePHP webroot
ENV APACHE_DOCUMENT_ROOT /var/www/html/app/webroot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
