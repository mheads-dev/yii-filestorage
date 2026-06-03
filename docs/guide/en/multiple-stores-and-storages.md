# Multiple stores and storages

## One `Storage`, Multiple Stores

Use when storage logic is shared but you need split by domain/channel.
Not only public/private.

Examples:

- `product-images` (public CDN),
- `documents` (private),
- `avatars` (public with dedicated baseUrl),
- `exports` (private, short retention).

```php
$storage = new Storage(
    repository: $repository,
    stores: [$imagesStore, $documentsStore, $avatarsStore],
    defaultStoreName: 'upload',
);
```

See example:

- [03-public-and-private.php](../../../examples/03-public-and-private.php)

## Multiple Named `Storage` Instances

Use when you need isolated storage contexts (for example multi-tenant).

```php
StorageProvider::set($defaultStorage); // default
StorageProvider::set($tenant42Storage, 'tenant-42');
```

`FileUpload` resolves storage through `fileClass::storage()`.
So for named storage, you usually need custom `fileClass` with overridden `storage()`.

See:

- [custom-file-class.md](custom-file-class.md)
- [07-active-record-custom-file-class-storage.php](../../../examples/07-active-record-custom-file-class-storage.php)
