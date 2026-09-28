# ImmoVerwalt

Interne Verwaltungsanwendung einer Haus- und Immobilienverwaltung:
Bestand und Stammdaten, Vermietung, Meldungen und Aufgaben, Termine sowie eine vereinfachte Abrechnung
für Mieter und Eigentümer.

**Technik:** PHP 7.4 · Symfony 4.4 · Twig · Doctrine ORM · Symfony Forms · PostgreSQL 13

## Start mit Docker

Voraussetzung: [Docker Desktop](https://www.docker.com/products/docker-desktop/) (unter Windows mit WSL 2)
und Git. PHP muss lokal **nicht** installiert sein.

```bash
git clone https://github.com/wolpert-coniant/thb_immoverwalt.git
cd thb_immoverwalt
docker compose up -d
```

Beim ersten Start lädt Docker ein fertiges Image mit allen PHP-Abhängigkeiten, legt das
Datenbankschema an und lädt Beispieldaten. Den Fortschritt zeigt:

```bash
docker compose logs -f php
```

Sobald `[immoverwalt] Bereit` erscheint, ist die Anwendung erreichbar unter **http://localhost:8080**.

| Benutzer    | Passwort    | Rolle                         |
|-------------|-------------|-------------------------------|
| `admin`     | `admin`     | Administration (alle Rechte)  |
| `verwalter` | `verwalter` | Objektverwaltung              |
| `vermieter` | `vermieter` | Vermietung                    |

## Nützliche Befehle

```bash
# Symfony-Konsole im Container
docker compose exec php php bin/console list

# Tests ausführen
docker compose exec php vendor/bin/phpunit

# Datenbank auf die Beispieldaten zurücksetzen (alle Änderungen gehen verloren)
docker compose exec php composer reset-db

# Nach Änderungen an docker/ oder am Dockerfile: Image lokal neu bauen
docker compose up -d --build

# Neueste Fassung holen (Code und Image)
git pull
docker compose pull
docker compose up -d

# Alles entfernen (Container, Datenbank, installierte Abhängigkeiten)
docker compose down -v
```

`vendor/` und `var/` liegen in Docker-Volumes und sind deshalb im Projektordner nicht sichtbar;
auf Windows und macOS wäre die Anwendung sonst sehr langsam.

Die Datenbank ist vom Rechner aus unter `localhost:5433` erreichbar
(Datenbank `immoverwalt`, Benutzer `immo`, Passwort `immo`).

## Verzeichnisse

```
src/Controller   Web-Controller (Twig-Seiten)
src/Entity       Doctrine-Entities
src/Form         Formulartypen
src/Repository   Doctrine-Repositories
src/Service      Services
templates/       Twig-Templates
migrations/      Doctrine-Migrationen
model/           Ihre Modelle: Regelkatalog, Fachmodell (Lehrveranstaltung)
tests/           PHPUnit-Tests
docker/          Docker-Konfiguration
```
