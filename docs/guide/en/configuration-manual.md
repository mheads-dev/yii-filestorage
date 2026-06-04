# Manual configuration

Use manual configuration in standalone scripts, tests, console utilities, or applications that do not use `yiisoft/config`.

## Basic setup

A working storage needs:

1. A repository for file metadata.
2. One or more stores for physical files.
3. Optional defaults for `add()` calls.

```php
use Mheads\Yii\Filestorage\Repository\DemoRepository;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageProvider;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

$repository = new DemoRepository('/app/runtime/demo-filestorage.json');

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

`DemoRepository` is intended for local examples and manual testing. For production metadata storage, install [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db), [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record), or implement a repository adapter.

## Next steps

- [Usage basics](usage.md)
- [Stores: public and private](stores.md)
- [Repositories](repositories.md)
