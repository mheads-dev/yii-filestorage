# Usage basics

`Storage` combines physical storage (`StoreInterface`) and metadata repository (`RepositoryInterface`).

Main methods:

- `add(UploadedFileInterface $file, ?string $groupName = null, ?string $storeName = null, ?string $description = null): FileInterface`
- `findById(int|string $id): ?FileInterface`
- `removeById(int|string $id): bool`
- `remove(FileInterface $file): void`
- `getUrl(FileInterface $file): ?string`
- `getContent(FileInterface $file): ?string`
- `getResource(FileInterface $file)`

Minimal example:

```php
$file = $storage->add(
    $uploadedFile,
    groupName: 'products',
    storeName: 'upload',
    description: 'Product image',
);
$same = $storage->findById($file->getId());
$url = $same?->getUrl();
if($same !== null) {
    $storage->remove($same);
}
```

See reference examples:

- [01-public-store.php](../../../examples/01-public-store.php)
- [02-private-store.php](../../../examples/02-private-store.php)
- [03-public-and-private.php](../../../examples/03-public-and-private.php)
