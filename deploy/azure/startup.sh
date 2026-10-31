#!/usr/bin/env bash
# App Service "Startup Command" (set in infra/app.bicep). Runs on every container start:
# the first boot after a deployment, and every wake-up after F1 idles the app out.
# Every step is idempotent, so running it on each start is safe.
set -euo pipefail

APP_DIR=/home/site/wwwroot
DB_FILE="${DB_DATABASE:-/home/data/portfolio.sqlite}"

# 1. Serve Laravel's public/ directory instead of wwwroot itself.
cp "$APP_DIR/deploy/azure/nginx.conf" /etc/nginx/sites-available/default
service nginx reload

cd "$APP_DIR"

# 2. The SQLite file lives on the persistent /home share, outside wwwroot, so deployments
#    (which replace wwwroot) never touch it.
mkdir -p "$(dirname "$DB_FILE")"
fresh_database=false
if [ ! -s "$DB_FILE" ]; then
    touch "$DB_FILE"
    fresh_database=true
fi

php artisan migrate --force --no-interaction

# Seeders write the résumé content with updateOrCreate, which would overwrite edits made in
# the admin panel. So they run exactly once, on a brand-new database; after that, /admin owns it.
if [ "$fresh_database" = true ]; then
    php artisan db:seed --force --no-interaction
fi

# 3. Caches are built here, not in CI: config:cache bakes in the App Service settings, which
#    only exist at runtime. views:warm compiles only what changed, so later wake-ups are quick.
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan filament:optimize
php artisan views:warm

# php-fpm may run as a different user from this script; make sure it can write.
chmod -R ug+rwX storage bootstrap/cache "$(dirname "$DB_FILE")" 2>/dev/null || true
