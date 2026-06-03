#!/bin/sh
set -eu

run_auto_setup() {
    if [ "${APP_AUTO_SETUP:-1}" = "0" ]; then
        return
    fi

    case "${1:-}" in
        frankenphp|caddy|php-server)
            ;;
        *)
            return
            ;;
    esac

    cd /app

    composer install --no-interaction --prefer-dist --no-progress

    php bin/console doctrine:migrations:migrate --no-interaction
}

run_auto_setup "$@"

exec docker-php-entrypoint "$@"
