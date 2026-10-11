# Development

This page is about working on the framework itself. Everything runs in [DDEV](https://ddev.com/), set up with PHP 8.5 and Apache and no database, and `ddev c` is short for `ddev composer`.

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations: lint, coding standard, PHPStan level 1.
ddev c c
# Run unit tests.
ddev c test
# Run unit tests, readable output.
ddev c test:d
```

---

## Validations

| Command | Runs |
| --- | --- |
| `ddev c check:lint` | `parallel-lint` on `config`, `public`, `resources`, `src` |
| `ddev c check:phpcs` | PHP_CodeSniffer with `.phpcs/php-coding-standard.xml` |
| `ddev c check:phpstan` | PHPStan with the shared `webservco/coding-standards` configuration |
| `ddev c s:1` ... `ddev c s:9` | PHPStan at that level, with `phpstan.neon` |
| `ddev c check:structure` | [PDS skeleton](https://github.com/php-pds/skeleton) validation |
| `ddev c check` (`c`) | `check:lint`, `check:phpcs`, `s:1` |

Most style errors can be fixed automatically:

```shell
ddev exec vendor/bin/phpcbf --standard=.phpcs/php-coding-standard.xml --extensions=php -p src tests
```

---

## Tests

Tests live in `tests/Unit/WebServCo/`, split into the `Test` and `Framework` suites defined in `phpunit.xml`. PHPUnit only picks up files ending in `Test.php`.

```shell
# One file.
ddev exec vendor/bin/phpunit tests/Unit/WebServCo/Framework/PathTest.php
# One test.
ddev exec vendor/bin/phpunit --filter canBeAccessedViaFramework
```

Libraries are shared between tests. Anything a test changes in one should be reset in `setUp()`, or the next test inherits it.

---

## Code style

- Coding standard: [webservco/coding-standards](https://packagist.org/packages/webservco/coding-standards) (PSR-12 and Slevomat).
- `declare(strict_types=1);` in every file, explicit `use function` and `use const` imports.
- Classes are `abstract` or `final`.
- PHPUnit tests use attributes (`#[Test]`), not annotations.

---

## Documentation

API documentation is generated with [phpDocumentor](https://www.phpdoc.org/), installed globally, and served by [Browsersync](https://browsersync.io/):

```shell
composer doc
```

It writes to `var/tmp/phpdoc`.

When a change affects behavior, update its page in `docs/` too. The [README](../README.md#documentation) has the index.
