# Internals

## Unit testing

Run the PHPUnit test suite:

```shell
./vendor/bin/phpunit
```

or:

```shell
make test
```

## Static analysis

Run Psalm:

```shell
./vendor/bin/psalm --no-cache
```

## Code style

Run PHP CS Fixer:

```shell
./vendor/bin/php-cs-fixer fix --dry-run --diff
```

## Examples

Example scripts live in [`examples`](../examples/README.md). They are reference/demo scripts and need project-specific paths/bootstrap adaptation.
