# mheads/yii-filestorage

A unified facade for working with uploaded files in Yii3 / yiisoft projects.

The package separates file handling into three responsibilities:

- `Storage` - one facade for `add()`, `findById()`, `remove()`, `getUrl()`, `getContent()`, and `getResource()`.
- `StoreInterface` - physical file storage, for example public or private filesystem storage.
- `RepositoryInterface` - file metadata storage, for example DB or ActiveRecord.

Use it when you want predictable file flows without scattering upload logic across controllers, services, and ActiveRecord lifecycle hooks.
The core usage is framework-agnostic `Storage` + `Repository` + `Store`; ActiveRecord support is optional.

## Requirements

- PHP 8.3 - 8.5.
- `yiisoft/db` `^2.0.1`.
- `yiisoft/files` `^2.1`.
- `yiisoft/strings` `^2.7`.
- `psr/http-message` `^2.0`.
- `yiisoft/active-record` `^1.0.2`.
- `yiisoft/event-dispatcher` `^1.1`.

Supported DB drivers: MySQL, PostgreSQL, MSSQL, SQLite, and Oracle.
Install the matching `yiisoft/db-*` package for your DBMS.

## Installation

Install the package with [Composer](https://getcomposer.org):

```shell
composer require mheads/yii-filestorage
```

Optional, if you want to apply package migrations through `yiisoft/db-migration`:

```shell
composer require --dev yiisoft/db-migration
```

Apply the migration for `mh_filestorage_file`:

- [Migrations](docs/guide/en/migrations.md)

## Quick Start

### 1. Configure storage

For manual configuration:

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

For `yiisoft/app` / `yiisoft/app-api`, see [Configuration with yiisoft/config](docs/guide/en/configuration-with-config.md).

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

### 3. Optional: use ActiveRecord upload lifecycle

```php
use Mheads\Yii\Filestorage\ActiveRecord\Event\Handler\FileUpload;

// In your AR model:
// #[FileUpload(groupName: 'products')]
// public int|string|null $picture_id = null;
// public function setPicture(?UploadedFileInterface $file): void { ... }

/** @var Product $product */
$product->setPicture($uploadedFile);
$product->save();
```

The file is uploaded before save, the file id is assigned to the model, and old files are auto-cleaned when configured.
See [AR file upload lifecycle](docs/guide/en/active-record-file-upload.md).

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)
- [Examples](examples/README.md)

## Feature Map

| Task | Where |
| --- | --- |
| Installation and requirements | [Prerequisites and installation](docs/guide/en/prerequisites-and-installation.md) |
| Manual configuration | [Manual configuration](docs/guide/en/configuration-manual.md) |
| DI/bootstrap for `yiisoft/app` | [Configuration with yiisoft/config](docs/guide/en/configuration-with-config.md) |
| Basic facade API | [Usage basics](docs/guide/en/usage.md) |
| Public and private stores | [Stores](docs/guide/en/stores.md) |
| DB and ActiveRecord repositories | [Repositories](docs/guide/en/repositories.md) |
| AR + `#[FileUpload]` lifecycle | [AR file upload lifecycle](docs/guide/en/active-record-file-upload.md) |
| Multiple stores and named storages | [Multiple stores and storages](docs/guide/en/multiple-stores-and-storages.md) |
| Multiple file-id fields in AR | [Two file fields](docs/guide/en/two-file-fields.md) |
| Custom repository/store adapters | [Custom adapters](docs/guide/en/custom-adapters.md) |
| Serving private files | [Serving private files](docs/guide/en/private-files.md) |
| Errors and exceptions | [Errors and exceptions](docs/guide/en/errors.md) |
| FAQ | [FAQ](docs/guide/en/faq.md) |

## Examples

`examples/*` are reference/demo scripts. Adapt them to your project runtime: DB connection, paths, bootstrap, and launch method.

See [examples/README.md](examples/README.md).
