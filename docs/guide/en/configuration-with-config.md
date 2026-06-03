# Configuration with yiisoft/config

Minimal DI config:

```php
<?php

use Mheads\Yii\Filestorage\Repository\DbRepository;
use Mheads\Yii\Filestorage\Repository\RepositoryInterface;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\Store\FileSystem\PrivateFileSystemStore;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

return [
    RepositoryInterface::class => [
        'class' => DbRepository::class,
    ],
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

Bootstrap (AR scenario):

```php
<?php

declare(strict_types=1);

use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\StorageProvider;
use Psr\Container\ContainerInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Connection\ConnectionProvider;

return [
    static function (ContainerInterface $container): void {
        ConnectionProvider::set($container->get(ConnectionInterface::class));
        StorageProvider::set($container->get(StorageInterface::class));
    },
];
```

For `yiisoft/app-api` and `yiisoft/app-console`, same scheme:
register `RepositoryInterface`/`StorageInterface` in DI + bootstrap with `ConnectionProvider::set(...)` and `StorageProvider::set(...)`.

Notes:

- `StorageProvider::set(...)` is needed for `FileInterface::getUrl()/getContent()/getResource()`.
- `ConnectionProvider::set(...)` is required for ActiveRecord scenarios (`ArFile`, `ActiveRecordRepository`, AR relation/queries).
  If AR is not used, you can skip it.

If project uses ActiveRecord and you want `findById()` / relation scenarios to work with `ArFile`,
prefer `ActiveRecordRepository`.

Example:

```php
use Mheads\Yii\Filestorage\Repository\ActiveRecordRepository;

return [
    StorageInterface::class => [
        'class' => Storage::class,
        '__construct()' => [
            'repository' => new ActiveRecordRepository(),
            'stores' => [
                // ...
            ],
        ],
    ],
];
```

`DbRepository` is also valid: it stores metadata via DB layer and creates `FileInterface` of the class
set in repository (`File` by default or your class). In AR model, file relation
is defined by the model itself (for example `hasOne(ArFile::class, ...)`), independent of repository choice.
