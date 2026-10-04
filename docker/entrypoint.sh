#!/bin/sh
# Migrations and caches run at container start, not at build time: the build
# has no database, and the caches must reflect this container's own env.
set -e

php artisan migrate --force --no-interaction

# storage/app/public must be reachable at public/storage. The symlink lives on
# a volume, so it is recreated on every start rather than baked into the image.
php artisan storage:link --force || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
