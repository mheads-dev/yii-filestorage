# Private files over HTTP

`PrivateFileSystemStore` must not be directly web-accessible.

Recommended pattern:

1. Resolve `FileInterface` via `Storage::findById()`.
2. Validate current user access in application business logic
   (for example: file belongs to their tenant/account, or role can access resource).
3. Return stream via HTTP response (`application/octet-stream` or exact content type).

Flow:

```php
$file = $storage->findById($id);
if($file === null) {
    // 404
}
// ACL check ...
$resource = $file->getResource();
// stream response
```

Never expose private-store path as static web-server URL.
