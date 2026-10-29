#!/bin/sh
set -eu

RUNTIME_ROOT=/tmp/apikawal
export LARAVEL_STORAGE_PATH="$RUNTIME_ROOT/storage"
export VIEW_COMPILED_PATH="$RUNTIME_ROOT/storage/framework/views"
export APP_CONFIG_CACHE="$RUNTIME_ROOT/bootstrap/cache/config.php"
export APP_EVENTS_CACHE="$RUNTIME_ROOT/bootstrap/cache/events.php"
export APP_PACKAGES_CACHE="$RUNTIME_ROOT/bootstrap/cache/packages.php"
export APP_ROUTES_CACHE="$RUNTIME_ROOT/bootstrap/cache/routes-v7.php"
export APP_SERVICES_CACHE="$RUNTIME_ROOT/bootstrap/cache/services.php"
export LOG_CHANNEL=stderr
export XDG_DATA_HOME="$RUNTIME_ROOT/caddy/data"
export XDG_CONFIG_HOME="$RUNTIME_ROOT/caddy/config"

mkdir -p \
    "$RUNTIME_ROOT/storage/app/private" \
    "$RUNTIME_ROOT/storage/app/public" \
    "$RUNTIME_ROOT/storage/framework/cache/data" \
    "$RUNTIME_ROOT/storage/framework/sessions" \
    "$RUNTIME_ROOT/storage/framework/views" \
    "$RUNTIME_ROOT/storage/logs" \
    "$RUNTIME_ROOT/bootstrap/cache" \
    "$XDG_DATA_HOME" \
    "$XDG_CONFIG_HOME"

exec "$@"
