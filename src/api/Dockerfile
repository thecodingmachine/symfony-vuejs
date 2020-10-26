FROM thecodingmachine/php:7.4-v3-apache

# PHP extensions.
ENV PHP_EXTENSION_REDIS="1" \
    PHP_EXTENSION_INTL="1" \
    PHP_EXTENSION_GD="1"

# PHP settings.
ENV PHP_INI_MEMORY_LIMIT="128M"

# Apache settings.
ENV APACHE_DOCUMENT_ROOT="public/"
RUN echo "\nServerTokens Prod\nServerSignature Off\n" | sudo tee -a /etc/apache2/sites-available/000-default.conf

# Copy files.
# Don't forget to create a .env file with required Symfony variables.
COPY --chown=docker:docker . .

# Dump .env to an optimized .env.local.php.
RUN composer dump-env prod

# Install dependencies.
RUN composer install --no-dev --optimize-autoloader

# Clear and warm-up cache.
RUN php bin/console cache:clear