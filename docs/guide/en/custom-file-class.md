# Custom file class

Use custom `ArFile`/`File` when you need to bind file model to non-default storage.

Example:

```php
final class TenantArFile extends ArFile
{
    public static function storage(): StorageInterface
    {
        return StorageProvider::get('tenant-42');
    }
}
```

Then set this class in `FileUpload` and/or repository:

```php
#[FileUpload(fileClass: TenantArFile::class)]
public ?int $picture_id = null;
```

```php
$repository = new ActiveRecordRepository(TenantArFile::class);
```

See reference example:

- [07-active-record-custom-file-class-storage.php](../../../examples/07-active-record-custom-file-class-storage.php)
- [support/TenantArFile.php](../../../examples/support/TenantArFile.php)
