#!/bin/sh
set -eu

cd /var/www/html

APACHE_PORT="${PORT:-80}"
case "$APACHE_PORT" in
    ''|*[!0-9]*)
        echo "ERROR: PORT must be a valid TCP port number." >&2
        exit 1
        ;;
esac
if [ "$APACHE_PORT" -lt 1 ] || [ "$APACHE_PORT" -gt 65535 ]; then
    echo "ERROR: PORT must be between 1 and 65535." >&2
    exit 1
fi

printf 'Listen %s\n' "$APACHE_PORT" > /etc/apache2/ports.conf
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${APACHE_PORT}>
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R u+rwX,g+rwX storage bootstrap/cache

attempt=1
migrated=0
while [ "$attempt" -le 5 ]; do
    echo "Running database migrations (attempt ${attempt}/5)."
    if timeout 5s php artisan migrate --force >/tmp/apikawal-migrate.log 2>&1; then
        migrated=1
        rm -f /tmp/apikawal-migrate.log
        break
    fi

    rm -f /tmp/apikawal-migrate.log
    if [ "$attempt" -lt 5 ]; then
        sleep 1
    fi
    attempt=$((attempt + 1))
done

if [ "$migrated" -ne 1 ]; then
    echo "ERROR: Database migrations failed after retries (about 30 seconds maximum). Check database availability and deployment environment settings." >&2
    exit 1
fi

if [ "${SEED_ON_BOOT:-false}" = "true" ]; then
    echo "SEED_ON_BOOT is enabled; running database seeders."
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R u+rwX,g+rwX storage bootstrap/cache

exec "$@"
