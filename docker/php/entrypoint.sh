#!/bin/sh
set -e

if [ "$1" = "php-fpm" ]; then
    # composer install nur, wenn vendor/ fehlt oder nicht zu composer.lock passt
    # (Zeilenenden ignorieren - unter Windows checkt Git mit CRLF aus)
    lock_hash=$(tr -d '\r' < composer.lock | md5sum | cut -d' ' -f1)
    if [ ! -f vendor/autoload.php ] || [ "$(cat vendor/.composer-lock.md5 2>/dev/null)" != "$lock_hash" ]; then
        echo "[immoverwalt] Installiere Abhängigkeiten (composer install) ..."
        composer install --prefer-dist --no-progress --no-interaction
        echo "$lock_hash" > vendor/.composer-lock.md5
    fi

    echo "[immoverwalt] Datenbankschema aktualisieren ..."
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

    echo "[immoverwalt] Beispieldaten laden (nur bei leerer Datenbank) ..."
    php bin/console app:seed-if-empty

    mkdir -p var/cache var/log
    chown -R www-data:www-data var || true
    echo "[immoverwalt] Bereit: http://localhost:8080"
fi

exec docker-php-entrypoint "$@"
