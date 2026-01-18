#!/bin/bash
set -e

# Run migrations (force for production)
echo "Running migrations..."
php artisan migrate --force

# Cache configuration and routes for performance
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the main process (supervisord/nginx/php-fpm from the base image)
echo "Starting server..."
exec /init
