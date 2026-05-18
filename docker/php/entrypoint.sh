#!/bin/sh
chown -R www-data:www-data /var/www
chmod -R 775 /var/www
# Instala dependências se vendor não existir
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    composer install --no-interaction --optimize-autoloader
fi

php artisan optimize

if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec php-fpm
fi