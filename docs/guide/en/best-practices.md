# Best practices

## Keep controllers thin

Use `StorageInterface` in application services instead of spreading upload, cleanup, and URL logic across controllers and ActiveRecord lifecycle hooks.

## Use public and private stores deliberately

Store only files that are safe to expose in public stores. Put protected files into private stores and serve them through application code after access checks.

## Register storage when file objects call storage methods

If code calls `$file->getUrl()`, `$file->getContent()`, or `$file->getResource()`, register the storage first:

```php
StorageProvider::set($storage);
```

For named storage instances, bind a custom file class through `storage()`. See [Custom file class](custom-file-class.md).

## Clean up replaced files intentionally

For ActiveRecord uploads, keep `enableAutoCleaning=true` unless your business logic needs historical files. Make setters clear the file-id field on `null`.

## Avoid exposing private paths

Never return filesystem paths from private stores to clients. Return streamed responses from application code instead. See [Serving private files](private-files.md).
