#!/bin/sh
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instala dependências se vendor não existir
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    composer install --no-interaction --optimize-autoloader
fi

php artisan optimize

if [ "$#" -gt 0 ]; then
    exec "$@"
else
    php artisan migrate
    exec php-fpm
fi