# Manual configuration

Use manual configuration in standalone scripts, tests, console utilities, or applications that do not use `yiisoft/config`.

## Basic setup

A working storage needs:

1. A repository for file metadata.
2. One or more stores for physical files.
3. Optional defaults for `add()` calls.

```php
use Mheads\Yii\Filestorage\Repository\DbRepository;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageProvider;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

$repository = new DbRepository($db);

$storage = new Storage(
    repository: $repository,
    stores: [
        new PublicFileSystemStore(
            name: 'upload',
            path: '/app/runtime/upload',
            baseUrl: 'https://cdn.example.com/upload',
        ),
    ],
    defaultStoreName: 'upload',
    defaultGroupName: 'common',
);

StorageProvider::set($storage);
```

`StorageProvider::set()` is required when you call `getUrl()`, `getContent()`, or `getResource()` on file objects directly.

## Next steps

- [Usage basics](usage.md)
- [Stores: public and private](stores.md)
- [Repositories: DB and ActiveRecord](repositories.md)
