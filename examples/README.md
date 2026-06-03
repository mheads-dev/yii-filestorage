# Examples

`vendor/.../examples/*` are reference/demo scripts for this library.
In real projects, adapt them to your runtime (DB connection, paths, bootstrap, launch method).

## Example Map

| Scenario | File |
| --- | --- |
| Public store + DB repository | [01-public-store.php](01-public-store.php) |
| Private store + DB repository | [02-private-store.php](02-private-store.php) |
| Multiple stores in one storage (public+private sample) | [03-public-and-private.php](03-public-and-private.php) |
| ActiveRecord repository (`ArFile`) | [04-active-record-repository.php](04-active-record-repository.php) |
| `#[FileUpload]` in AR model | [05-active-record-file-upload.php](05-active-record-file-upload.php) |
| Two `#[FileUpload]` attributes in one model | [06-active-record-two-file-upload.php](06-active-record-two-file-upload.php) |
| Multi-storage + custom `ArFile::storage()` | [07-active-record-custom-file-class-storage.php](07-active-record-custom-file-class-storage.php) |

## Preparation

1. Prepare `ConnectionInterface` (from your app DI container).
2. Apply migration for `mh_filestorage_file`:
   - [../migrations/M260421000001CreateFileStorage.php](../migrations/M260421000001CreateFileStorage.php)
3. Helper [support/UploadedFileFactory.php](support/UploadedFileFactory.php) requires `httpsoft/http-message`.
4. AR examples (`04`-`07`) require `yiisoft/active-record` and `yiisoft/event-dispatcher`.
5. `05`/`06`/`07` require `product` table:
   - fields: `id`, `name`, `picture_id`, `manual_id`
   - ready SQL samples:
     - [migrations/mysql-product.sql](migrations/mysql-product.sql)
     - [migrations/pgsql-product.sql](migrations/pgsql-product.sql)
     - [migrations/mssql-product.sql](migrations/mssql-product.sql)
6. Create file storage directories (or keep directory creation from samples).

## How To Run In Your Project

1. Copy `examples` folder into your project.
2. Implement `getDbConnection()` in [support/getDbConnection.php](support/getDbConnection.php) using your project’s connection pattern.
3. Run script via your PHP launch flow (CLI/container/build scripts).

Important: samples are not intended to run directly from `vendor/.../examples` without path/environment adaptation.

## Minimal `Connection` Example

All scripts use shared helper [support/getDbConnection.php](support/getDbConnection.php): replace the stub with your real app connection.

This snippet additionally requires `yiisoft/cache` (for example `ArrayCache`).

```php
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Mysql\Connection;
use Yiisoft\Db\Mysql\Driver;
use Yiisoft\Db\Mysql\Dsn;

function getDbConnection(): Connection
{
    return new Connection(
        new Driver(
            new Dsn('mysql', 'db', 'app', '3306'),
            'user',
            'supersecretpassword',
        ),
        new SchemaCache(new ArrayCache()),
    );
}
```

## Notes

- Samples demonstrate API and typical lifecycle, not production architecture.
- If you call `getUrl()/getContent()/getResource()` on file objects, register storage in `StorageProvider`.
