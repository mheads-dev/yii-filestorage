# FAQ

## `getUrl()` Returns `null` Or Fails

Check:

- file is stored in a public store
- store has correct `baseUrl`
- storage is registered in `StorageProvider` if you call `$file->getUrl()` directly

Private stores always return `null` for URL.

## File Is Not Removed After Replacement

The core package does not track domain-object lifecycle automatically. When a file is replaced, call `StorageInterface::remove()` or `removeById()` for the old file according to your business rules.

## Why Is Custom `fileClass` Needed?

When the default storage is not enough, for example in multi-storage or multi-tenant setups, custom file classes can bind file objects to a named storage by overriding `storage()`. See [Multiple stores and storages](multiple-stores-and-storages.md).
