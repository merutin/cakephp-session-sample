#!/bin/bash
set -e

# Install Composer dependencies if vendor directory doesn't exist
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Installing Composer dependencies..."
    cd /var/www/html
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=php
fi

# Set permissions
chown -R www-data:www-data /var/www/html/tmp
chown -R www-data:www-data /var/www/html/logs
chmod -R 777 /var/www/html/tmp
chmod -R 777 /var/www/html/logs

# Start Apache
exec apache2-foreground
