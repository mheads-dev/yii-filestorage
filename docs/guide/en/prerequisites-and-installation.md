# Prerequisites and installation

## Requirements

- PHP 8.3 - 8.5.
- `yiisoft/files` `^2.1`.
- `yiisoft/strings` `^2.7`.
- `psr/http-message` `^2.0`.

DB and ActiveRecord integrations are not part of the core package.
Use [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db), [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record), or implement `RepositoryInterface` in your application.

## Installation

Install the package with [Composer](https://getcomposer.org):

```shell
composer require mheads/yii-filestorage
```

## Next steps

- [Configuration with yiisoft/config](configuration-with-config.md)
- [Manual configuration](configuration-manual.md)
- [Custom repository and store adapters](custom-adapters.md)
