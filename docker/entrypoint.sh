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

    echo "Waiting for database..."

    until pg_isready -h "${POSTGRES_HOST:-database}" -p "${POSTGRES_PORT:-5432}" -U "${POSTGRES_USER:-app}" -d "${POSTGRES_DB:-app}" >/dev/null 2>&1; do
        sleep 1
    done

    php bin/console doctrine:migrations:migrate --no-interaction
}

run_auto_setup "$@"

exec docker-php-entrypoint "$@"
