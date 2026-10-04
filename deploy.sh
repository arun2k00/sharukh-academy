#!/usr/bin/env bash
# Deploy to a Hostinger shared-hosting target.
#
#   ./deploy.sh sandbox     -> sharuk.azflow.io
#   ./deploy.sh production  -> lms.sharukhacademy.com
#
# The layout on these accounts splits the app in two: the code lives in a
# private directory, and only the contents of public/ sit in the web root.
# Both halves must be updated or the site serves new HTML against stale CSS,
# which is exactly how the last deploy broke. The web root's own index.php is
# patched to point at the app directory, so it is never overwritten, and the
# storage symlink there is left alone.
set -euo pipefail

TARGET="${1:-}"
case "$TARGET" in
  sandbox)
    SSH_HOST="sharukh-sandbox"
    APP_DIR="domains/azflow.io/sharukh_app"
    WEB_DIR="domains/azflow.io/public_html/sharukh"
    ;;
  production)
    SSH_HOST="sharukh-prod"
    APP_DIR="domains/sharukhacademy.com/lms_app"
    WEB_DIR="domains/sharukhacademy.com/public_html/lms"
    ;;
  *)
    echo "usage: ./deploy.sh {sandbox|production}" >&2; exit 1 ;;
esac

PHP="${REMOTE_PHP:-/opt/alt/php84/usr/bin/php}"
STAMP=$(date +%Y%m%d_%H%M%S)

echo "==> Building assets"
npm run build

echo "==> Packing"
TMP=$(mktemp -d)
tar -czf "$TMP/app.tar.gz" \
  --exclude='.git' --exclude='node_modules' --exclude='vendor' --exclude='storage' \
  --exclude='.env' --exclude='tests' --exclude='.DS_Store' --exclude='.phpunit.result.cache' \
  --exclude='.kiro' --exclude='.claude' --exclude='public/storage' --exclude='public/hot' \
  --exclude='bootstrap/cache' .
tar -czf "$TMP/public.tar.gz" -C public \
  --exclude='storage' --exclude='index.php' --exclude='.DS_Store' .

echo "==> Backing up remote app dir"
ssh "$SSH_HOST" "cd ~/$(dirname "$APP_DIR") && tar -czf ~/backup_${TARGET}_${STAMP}.tar.gz --exclude=$(basename "$APP_DIR")/vendor $(basename "$APP_DIR")" || \
  echo "    (no existing install to back up)"

echo "==> Uploading"
scp "$TMP/app.tar.gz" "$TMP/public.tar.gz" "$SSH_HOST:/tmp/"

echo "==> Unpacking and migrating"
ssh "$SSH_HOST" bash -s <<REMOTE
set -euo pipefail
mkdir -p ~/$APP_DIR ~/$WEB_DIR
tar -xzf /tmp/app.tar.gz -C ~/$APP_DIR 2>/dev/null
tar -xzf /tmp/public.tar.gz -C ~/$WEB_DIR 2>/dev/null
rm -f /tmp/app.tar.gz /tmp/public.tar.gz
find ~/$WEB_DIR -name '._*' -delete
cd ~/$APP_DIR
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3 || true
# composer's post-install hook shells out, and proc_open is disabled here.
$PHP artisan package:discover
$PHP artisan migrate --force
$PHP artisan optimize:clear
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
chmod -R 755 storage bootstrap/cache
REMOTE

rm -rf "$TMP"
echo "==> Done. Backup: ~/backup_${TARGET}_${STAMP}.tar.gz"
