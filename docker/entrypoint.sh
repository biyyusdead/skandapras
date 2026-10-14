#!/bin/sh
set -e

cd /var/www/html

# Buat direktori writable
mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache


# Storage link
if [ ! -L public/storage ]; then
    ln -sf $(pwd)/storage/app/public $(pwd)/public/storage
fi

# Build Vite frontend assets jika manifest belum ada
if [ ! -f "public/build/manifest.json" ] && [ -f "package.json" ]; then
    echo "Manifest build belum ada, menjalankan npm run build..."
    if [ ! -d "node_modules" ]; then
        npm install
    fi
    npm run build 2>&1 || true
fi

chown -R www-data:www-data storage bootstrap/cache public/storage public/build 2>/dev/null || chmod -R ug+rwX storage bootstrap/cache public/storage public/build 2>/dev/null || true

# Marker untuk track DB initialization
MARKER_FILE="storage/.db_initialized"

if [ ! -f "$MARKER_FILE" ]; then
    echo "Menunggu MySQL siap..."
    
    if [ -n "${DB_HOST}" ] && [ -n "${DB_USERNAME}" ] && [ -n "${DB_PASSWORD}" ]; then
        MAX_TRIES=30
        COUNT=0
        while [ "$COUNT" -lt "$MAX_TRIES" ]; do
            if mysqladmin ping -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --ssl-mode=DISABLED --silent 2>/dev/null; then
                echo "MySQL siap."
                break
            fi
            echo "DB wait... ($((COUNT + 1))/$MAX_TRIES)"
            sleep 2
            COUNT=$((COUNT + 1))
        done
    fi
    
    echo "Menjalankan migrations & seeders..."
    if php artisan migrate:fresh --seed --force 2>&1; then
        touch "$MARKER_FILE"
        echo "✓ DB initialized."
    else
        echo "✗ Migration/seed gagal. Check logs."
    fi
else
    echo "Database sudah initialized."
fi

# Cache (optional)
php artisan config:cache >/dev/null 2>&1 || true

exec "$@"
