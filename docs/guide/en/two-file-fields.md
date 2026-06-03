# Two file fields in one AR model

Limitation: one `#[FileUpload]` handles exactly one file-id field.

If model has two fields (`picture_id`, `manual_id`), use two attributes:

```php
#[FileUpload(groupName: 'products', storeName: 'upload', fileClass: ArFile::class)]
public ?int $picture_id = null;

#[FileUpload(groupName: 'products', storeName: 'upload', fileClass: ArFile::class)]
public ?int $manual_id = null;
```

Each field needs its own setter that calls `queuePendingUploadedFile(...)`.

See reference example:

- [06-active-record-two-file-upload.php](../../../examples/06-active-record-two-file-upload.php)
- [support/ProductWithTwoFiles.php](../../../examples/support/ProductWithTwoFiles.php)
