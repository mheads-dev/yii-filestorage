# Migrations

Package uses table `mh_filestorage_file`.

Migration:

- [M260421000001CreateFileStorage.php](../../../migrations/M260421000001CreateFileStorage.php)

## Wiring Into `yiisoft/db-migration`

Via path:

```php
'yiisoft/db-migration' => [
    'sourcePaths' => [
        dirname(__DIR__, 2) . '/vendor/mheads/yii-filestorage/migrations',
    ],
],
```

Or via your centralized migration package/directory.

## Important Check

- for Oracle, make sure sequence/trigger from migration are applied.
