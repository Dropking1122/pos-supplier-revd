#!/bin/bash
cd /home/runner/workspace

# Ensure SQLite database file exists
touch database/database.sqlite

# Patch .env with correct APP_URL for this environment
if [ -f .env ]; then
    if [ -n "$REPLIT_DEV_DOMAIN" ]; then
        sed -i "s|^APP_URL=.*|APP_URL=https://$REPLIT_DEV_DOMAIN|" .env
    fi
fi

# Ensure DB settings point to SQLite
grep -q "^DB_CONNECTION=" .env && sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env || echo "DB_CONNECTION=sqlite" >> .env
grep -q "^DB_DATABASE=" .env && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=/home/runner/workspace/database/database.sqlite|" .env || echo "DB_DATABASE=/home/runner/workspace/database/database.sqlite" >> .env

# Remove MySQL-specific env vars if present
sed -i '/^DB_HOST=/d' .env
sed -i '/^DB_PORT=/d' .env
sed -i '/^DB_USERNAME=/d' .env
sed -i '/^DB_PASSWORD=/d' .env
sed -i '/^DB_SOCKET=/d' .env

php artisan config:clear 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true
php artisan migrate --force 2>/dev/null || true
php artisan db:seed --force 2>/dev/null || true

php artisan serve --host=0.0.0.0 --port=5000
