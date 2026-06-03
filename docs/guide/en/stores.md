# Stores: public and private

Stores handle physical file storage. The package includes filesystem stores for public and private files.

## Public store

Use `PublicFileSystemStore` when files may be served directly by a web server or CDN.

```php
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

$store = new PublicFileSystemStore(
    name: 'upload',
    path: '/app/public/upload',
    baseUrl: '/upload',
);
```

Files stored in a public store support `getUrl()`.

## Private store

Use `PrivateFileSystemStore` when files must not be directly web-accessible.

```php
use Mheads\Yii\Filestorage\Store\FileSystem\PrivateFileSystemStore;

$store = new PrivateFileSystemStore(
    name: 'documents',
    path: '/app/runtime/private-upload',
);
```

Private files should be served through application logic after access checks. See [Serving private files](private-files.md).

## Multiple stores

One `Storage` can use multiple stores and choose the target via `storeName` in `add()`.

See [Multiple stores and storages](multiple-stores-and-storages.md).
