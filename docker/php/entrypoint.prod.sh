#!/bin/sh
set -e

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Only run Laravel caching when starting the main app (not queue/reverb workers)
if [ "$1" = "/usr/bin/supervisord" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link --quiet || true
fi

exec "$@"
