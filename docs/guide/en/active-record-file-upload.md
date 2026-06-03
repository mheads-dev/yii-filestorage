# AR file upload lifecycle

`#[FileUpload]` automates file-id field lifecycle in AR models.

Recommended model pattern:

1. Model implements `PendingUploadedFileOwnerInterface`.
2. Model uses `PendingUploadedFileOwnerTrait`.
3. Upload field setter calls `queuePendingUploadedFile('<file_id_column>', $uploadedFile)`.
4. On `null`, setter clears file-id column: `$this->set('<file_id_column>', null)`.

Example:

```php
#[FileUpload(groupName: 'products', storeName: 'upload', fileClass: ArFile::class)]
public ?int $picture_id = null;

public function setPicture(?UploadedFileInterface $uploadedFile): void
{
    $this->queuePendingUploadedFile('picture_id', $uploadedFile);
    if($uploadedFile === null) {
        $this->set('picture_id', null);
    }
}
```

Behavior:

- `BeforeSave`: uploads pending file, writes new id to model.
- `AfterSave`: removes old file (if `enableAutoCleaning=true` and id changed).
- `AfterDelete`: removes linked file (if `enableAutoCleaning=true`).
- Shutdown cleanup: if `BeforeSave` uploaded a new file but save flow did not finish successfully,
  the handler attempts to remove the newly uploaded file.

See reference example:

- [05-active-record-file-upload.php](../../../examples/05-active-record-file-upload.php)
