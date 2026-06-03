# Prerequisites and installation

## Requirements

- PHP 8.3 - 8.5.
- `yiisoft/db` `^2.0.1`.
- `yiisoft/files` `^2.1`.
- `yiisoft/strings` `^2.7`.
- `psr/http-message` `^2.0`.
- `yiisoft/active-record` `^1.0.2`.
- `yiisoft/event-dispatcher` `^1.1`.

## Supported databases

Install the matching DB driver for your DBMS:

- MySQL: `yiisoft/db-mysql`.
- PostgreSQL: `yiisoft/db-pgsql`.
- MSSQL: `yiisoft/db-mssql`.
- SQLite: `yiisoft/db-sqlite`.
- Oracle: `yiisoft/db-oracle`.

## Installation

Install the package with [Composer](https://getcomposer.org):

```shell
composer require mheads/yii-filestorage
```

Optional, if you want to apply package migrations through `yiisoft/db-migration`:

```shell
composer require --dev yiisoft/db-migration
```

## Next steps

- [Migrations](migrations.md)
- [Configuration with yiisoft/config](configuration-with-config.md)
- [Manual configuration](configuration-manual.md)
