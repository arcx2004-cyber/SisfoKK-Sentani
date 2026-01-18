FROM serversideup/php:8.3-fpm-nginx

# Switch to root to install dependencies
USER root

# Switch back to the non-root user 'www-data' (default in this image)
USER www-data

# Copy Composer files
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install Composer dependencies (no autoloader yet)
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-autoloader --ignore-platform-reqs

# Copy the rest of the application code
COPY --chown=www-data:www-data . .

# Generate autoload files and run post-install scripts
RUN composer dump-autoload --optimize

# Copy custom entrypoint
COPY --chown=www-data:www-data docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
