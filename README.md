# Wallet

[![CI](https://github.com/akudovich/wallet/actions/workflows/ci.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/ci.yaml)
[![Composer](https://github.com/akudovich/wallet/actions/workflows/composer-validate.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/composer-validate.yaml)
[![Security](https://github.com/akudovich/wallet/actions/workflows/security-audit.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/security-audit.yaml)
[![Code Style](https://github.com/akudovich/wallet/actions/workflows/code-style.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/code-style.yaml)
[![PHPStan](https://github.com/akudovich/wallet/actions/workflows/phpstan.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/phpstan.yaml)
[![Symfony Container](https://github.com/akudovich/wallet/actions/workflows/symfony-container.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/symfony-container.yaml)
[![Doctrine Mapping](https://github.com/akudovich/wallet/actions/workflows/doctrine-mapping.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/doctrine-mapping.yaml)
[![Tests](https://github.com/akudovich/wallet/actions/workflows/tests.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/tests.yaml)
[![Codecov](https://codecov.io/gh/akudovich/wallet/branch/master/graph/badge.svg)](https://app.codecov.io/gh/akudovich/wallet)

Simple wallet service built with Symfony, Doctrine ORM, and PostgreSQL.

## Requirements

- Docker
- Docker Compose

The application requires PHP `^8.4`. In the local environment PHP is provided by
the `php` Docker service.

## Getting Started

Build and start the services:

```bash
docker compose up -d --build
```

Install Composer dependencies:

```bash
docker compose exec php composer install
```

Run database migrations:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

The application is available at:

```text
http://localhost
```

## Database

Default development database connection:

```text
postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8
```

`compose.override.yaml` exposes PostgreSQL to the host:

```text
127.0.0.1:5432
database: app
user: app
password: !ChangeMe!
```

## API

Get a wallet balance:

```http
GET /v1/balance/1
```

Update a wallet balance:

```http
POST /v1/balance/1/debit/stock
Content-Type: application/json

{
  "amount": 100,
  "currency": "RUB"
}
```

Supported values:

- transaction type: `debit`, `credit`
- transaction reason: `stock`, `refund`
- currency: `RUB`, `USD`

## Useful Commands

Run the code style check:

```bash
docker compose exec php composer check-cs
```

Fix code style issues:

```bash
docker compose exec php composer fix-cs
```

Run PHPStan:

```bash
docker compose exec php composer phpstan
```

Prepare the test database:

```bash
docker compose exec php composer test-setup
```

Run tests:

```bash
docker compose exec php composer phpunit
```

Generate code coverage:

```bash
docker compose exec php composer coverage
```

The coverage report is generated in:

```text
var/coverage
```

Run local QA checks:

```bash
docker compose exec php composer qa
```

Run the same checks as CI:

```bash
docker compose exec php composer ci
```

## CI

GitHub Actions workflows are located in:

```text
.github/workflows
```

The aggregate `CI` workflow runs on PHP `8.4` and `8.5` and executes:

- `composer validate --strict`
- `composer audit`
- Symfony container lint
- Doctrine mapping validation
- ECS
- PHPStan
- PHPUnit

Separate workflows are also configured for individual README badges.
