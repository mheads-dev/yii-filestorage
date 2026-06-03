# Internals

## Unit testing

Run the PHPUnit test suite:

```shell
./vendor/bin/phpunit
```

## Static analysis

Run Psalm:

```shell
./vendor/bin/psalm
```

## Code style

Run PHP CS Fixer:

```shell
./vendor/bin/php-cs-fixer fix --dry-run --diff
```

## Examples

Example scripts live in [`examples`](../examples/README.md). They are reference/demo scripts and need project-specific DB/bootstrap adaptation.
