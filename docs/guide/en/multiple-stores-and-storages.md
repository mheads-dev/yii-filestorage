# Multiple stores and storages

## One `Storage`, Multiple Stores

Use one `Storage` with multiple stores when storage logic is shared but files must be split by domain, visibility, or lifecycle.

Examples:

- `product-images` (public CDN)
- `documents` (private)
- `avatars` (public with dedicated base URL)
- `exports` (private, short retention)

```php
$storage = new Storage(
    repository: $repository,
    stores: [$imagesStore, $documentsStore, $avatarsStore],
    defaultStoreName: 'upload',
);
```

Choose a target store per upload with `storeName`:

```php
$file = $storage->add(
    uploadedFile: $uploadedFile,
    groupName: 'documents',
    storeName: 'documents',
);
```

See example:

- [03-public-and-private.php](../../../examples/03-public-and-private.php)

## Multiple Named `Storage` Instances

Use multiple named storage instances when you need isolated storage contexts, for example multi-tenant storage.

```php
StorageProvider::set($defaultStorage); // default
StorageProvider::set($tenant42Storage, 'tenant-42');
```

File objects call `FileInterface::storage()` when you use `$file->getUrl()`, `$file->getContent()`, or `$file->getResource()` directly. The default `File` class resolves the default storage. For a named storage, create a file class that overrides `storage()`:

```php
use Mheads\Yii\Filestorage\Entity\File;
use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\StorageProvider;

final class Tenant42File extends File
{
    public static function storage(): StorageInterface
    {
        return StorageProvider::get('tenant-42');
    }
}
```

Your repository adapter must create and return that file class for tenant-specific files.
