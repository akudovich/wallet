# Wallet

**Quality**

[![Composer](https://github.com/akudovich/wallet/actions/workflows/composer-validate.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/composer-validate.yaml)
[![Security](https://github.com/akudovich/wallet/actions/workflows/security-audit.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/security-audit.yaml)
[![Code Style](https://github.com/akudovich/wallet/actions/workflows/code-style.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/code-style.yaml)
[![PHPStan](https://github.com/akudovich/wallet/actions/workflows/phpstan.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/phpstan.yaml)
[![Architecture](https://github.com/akudovich/wallet/actions/workflows/architecture.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/architecture.yaml)

**Framework**

[![Symfony Container](https://github.com/akudovich/wallet/actions/workflows/symfony-container.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/symfony-container.yaml)
[![Doctrine Mapping](https://github.com/akudovich/wallet/actions/workflows/doctrine-mapping.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/doctrine-mapping.yaml)

**Tests**

[![Tests](https://github.com/akudovich/wallet/actions/workflows/tests.yaml/badge.svg)](https://github.com/akudovich/wallet/actions/workflows/tests.yaml)
[![Codecov](https://codecov.io/gh/akudovich/wallet/branch/master/graph/badge.svg)](https://app.codecov.io/gh/akudovich/wallet)

**Stats**

![GitHub repo file count](https://img.shields.io/github/directory-file-count/akudovich/wallet)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/akudovich/wallet)

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

On startup, the PHP container installs Composer dependencies, waits for PostgreSQL, and runs Doctrine migrations automatically.

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

## Race Conditions

Balance updates are protected at the database level.

The application does not read a wallet balance, calculate a new value in PHP, and
then write it back. That pattern would be vulnerable to lost updates when two
requests update the same wallet at the same time.

Instead, wallet changes are performed with atomic SQL statements inside a
database transaction:

- debit uses `INSERT ... ON CONFLICT ... DO UPDATE` and increments the stored
  balance in PostgreSQL
- credit uses a conditional `UPDATE` with `balance_amount >= :amount`
- currency checks are part of the same SQL condition
- transaction history and balance update are wrapped in one Doctrine transaction

For example, credit is implemented as a single guarded update:

```sql
UPDATE wallet
SET balance_amount = balance_amount - :amount
WHERE id = :id
  AND balance_currency = :currency
  AND balance_amount >= :amount
```

If two concurrent credit requests try to spend the same balance, PostgreSQL
serializes writes to the affected row. The first successful update changes the
balance, and the second request re-checks the `WHERE` condition against the
current row version. If there is not enough money anymore, no row is updated and
the application returns an insufficient funds error.

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

Run architecture checks:

```bash
docker compose exec php composer deptrac
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

## Architecture Checks

Architecture rules are checked with Deptrac. The configuration is located at:

```text
deptrac.yaml
```

The current layers are:

- `PSR Contracts` for `Psr\\*` interfaces
- `Doctrine Attributes` for `Doctrine\\ORM\\Mapping\\*`
- `Doctrine Runtime` for Doctrine DBAL/ORM runtime services
- `Symfony UI` for Symfony HTTP, routing, validation, and serialization classes
- `Domain`
- `Application`
- `Infrastructure`
- `UI`

The dependency direction follows Clean Architecture:

- `Domain` can depend on `PSR Contracts` and `Doctrine Attributes`
- `Application` can depend on `Domain` and `PSR Contracts`
- `Infrastructure` can depend on `Application`, `Domain`, `PSR Contracts`, and `Doctrine Runtime`
- `UI` can depend on `Application`, `Domain`, `PSR Contracts`, and `Symfony UI`

Doctrine attributes are allowed in the domain intentionally as a pragmatic
Doctrine ORM mapping choice.

Run the default architecture check:

```bash
docker compose exec php composer deptrac
```

Show uncovered dependencies:

```bash
docker compose exec php vendor/bin/deptrac analyse \
  --cache-file=var/deptrac/cache \
  --report-uncovered
```

Generate a JSON report:

```bash
docker compose exec php vendor/bin/deptrac analyse \
  --cache-file=var/deptrac/cache \
  --formatter=json \
  --output=var/deptrac/report.json
```

Generate a Mermaid graph:

```bash
docker compose exec php vendor/bin/deptrac analyse \
  --cache-file=var/deptrac/cache \
  --formatter=mermaidjs \
  --output=var/deptrac/architecture.mmd
```

## CI

GitHub Actions workflows are located in:

```text
.github/workflows
```

Each check has its own workflow and README badge:

- `composer-validate.yaml` runs `composer validate --strict`
- `security-audit.yaml` runs `composer audit --locked`
- `code-style.yaml` runs ECS
- `architecture.yaml` runs Deptrac
- `phpstan.yaml` runs PHPStan
- `symfony-container.yaml` lints the Symfony container
- `doctrine-mapping.yaml` validates Doctrine mapping
- `tests.yaml` runs PHPUnit
- `coverage.yaml` generates coverage and uploads it to Codecov

The same local checks can be run with:

```bash
docker compose exec php composer ci
```
