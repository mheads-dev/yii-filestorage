# mheads/yii-filestorage

A unified facade for working with uploaded files in Yii3 / yiisoft projects.

The package separates file handling into three responsibilities:

- `Storage` - one facade for `add()`, `findById()`, `remove()`, `getUrl()`, `getContent()`, and `getResource()`.
- `StoreInterface` - physical file storage, for example public or private filesystem storage.
- `RepositoryInterface` - file metadata storage.

The core package contains the facade, file entity, repository/store contracts, filesystem stores, and a lightweight demo repository for local examples. DB and ActiveRecord integrations live in separate adapter packages: [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db) and [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record).

## Requirements

- PHP 8.3 - 8.5.
- `yiisoft/files` `^2.1`.
- `yiisoft/strings` `^2.7`.
- `psr/http-message` `^2.0`.

## Installation

Install the package with [Composer](https://getcomposer.org):

```shell
composer require mheads/yii-filestorage
```

## Adapters

Install an adapter when file metadata must be stored outside the demo repository:

- [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db) - DB metadata repository and migrations.
- [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record) - ActiveRecord integration. Depends on `mheads/yii-filestorage-db`.

## Quick Start

### 1. Configure storage

For manual configuration:

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

For production metadata storage, install [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db), [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record), or implement a repository adapter.

### 2. Add and read a file

```php
/** @var \Mheads\Yii\Filestorage\StorageInterface $storage */

$file = $storage->add($uploadedFile, groupName: 'products');

$url = $storage->getUrl($file);
$content = $storage->getContent($file);
$resource = $storage->getResource($file);

$sameFile = $storage->findById($file->getId());
if($sameFile !== null) {
    $storage->remove($sameFile);
}
```

See [Usage basics](docs/guide/en/usage.md) for details.

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)
- [Examples](examples/README.md)

## Examples

`examples/*` are reference/demo scripts. Adapt them to your project runtime: paths, bootstrap, and launch method.

See [examples/README.md](examples/README.md).

## License

The package is released under the [BSD 3-Clause License](LICENSE.md).
