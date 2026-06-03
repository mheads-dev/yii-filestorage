# FAQ

## `getUrl()` Returns `null` Or Fails

Check:

- file is stored in a public store
- store has correct `baseUrl`
- storage is registered in `StorageProvider`

## File Is Not Removed On AR Replace/Delete

Check:

- `FileUpload` has `enableAutoCleaning=true`
- model uses `PendingUploadedFileOwnerTrait`
- setter clears file-id field on `null` via `$this->set(...)`

## Why Is Custom `fileClass` Needed?

When `default` storage is not enough (multi-storage/multi-tenant), custom `fileClass` binds file model to the required storage via `storage()`.
