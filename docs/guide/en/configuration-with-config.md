# Configuration with yiisoft/config

Minimal DI config:

```php
<?php

use Mheads\Yii\Filestorage\Repository\DemoRepository;
use Mheads\Yii\Filestorage\Repository\RepositoryInterface;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\Store\FileSystem\PrivateFileSystemStore;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

return [
    RepositoryInterface::class => static fn() => new DemoRepository(dirname(__DIR__, 3) . '/runtime/demo-filestorage.json'),
    StorageInterface::class => [
        'class' => Storage::class,
        '__construct()' => [
            'stores' => [
                new PublicFileSystemStore(
                    name: Storage::DEFAULT_STORE_NAME,
                    path: dirname(__DIR__, 3) . '/public/upload',
                    baseUrl: '/upload',
                ),
                new PrivateFileSystemStore(
                    name: Storage::DEFAULT_STORE_NAME . '_private',
                    path: dirname(__DIR__, 3) . '/upload-private',
                ),
            ],
        ],
    ],
];
```

Bootstrap:

```php
<?php

use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\StorageProvider;
use Psr\Container\ContainerInterface;

return [
    static function (ContainerInterface $container): void {
        StorageProvider::set($container->get(StorageInterface::class));
    },
];
```

Notes:

- `StorageProvider::set(...)` is needed for `FileInterface::getUrl()/getContent()/getResource()`.
- Replace `DemoRepository` with [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db), [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record), or your own production repository adapter when metadata must be persistent and concurrent-safe.
