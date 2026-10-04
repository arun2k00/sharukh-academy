# Sharukh Academy — single-container Laravel image for the vvork VPS.
#
# FrankenPHP rather than nginx + php-fpm + supervisor: one process, one
# container, no process manager to babysit. TLS is terminated by the VPS's
# shared edge-proxy, so this only ever speaks plain HTTP on 80.

# --- assets -----------------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
COPY public ./public
RUN npm run build

# --- php dependencies -------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /build
COPY composer.json composer.lock ./
# Scripts need the full tree (artisan, bootstrap); skip them and dump autoload
# after the source is copied in the final stage.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# --- runtime ----------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.4

RUN install-php-extensions pdo_mysql gd zip intl bcmath opcache exif

# Only for the autoload dump below; the runtime never installs packages.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .
COPY --from=vendor /build/vendor ./vendor
COPY --from=assets /build/public/build ./public/build

# One mkdir per path: /bin/sh here is dash, which does not expand braces, so a
# {a,b,c} list silently creates a single literal directory instead of three.
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && rm -rf tests .git node_modules \
    && find . -name '._*' -delete \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/app/public storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# FrankenPHP's bundled Caddyfile already serves public/ with php_server;
# SERVER_NAME is all it needs. TLS is the edge-proxy's job, so plain :80.
ENV SERVER_NAME=:80

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
