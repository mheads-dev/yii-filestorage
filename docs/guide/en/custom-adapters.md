# Custom repository and store adapters

The package is built around contracts, so applications can replace metadata and physical-storage backends independently.

## Repository (Metadata)

Implement [RepositoryInterface](../../../src/Repository/RepositoryInterface.php) when file metadata is stored outside the built-in `DemoRepository`.

Typical options:

- project database table or schema
- external HTTP/gRPC service
- document/key-value storage

For ready DB and ActiveRecord integrations, use adapter packages:

- [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db)
- [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record)

## Store (Physical Files)

Implement [StoreInterface](../../../src/Store/StoreInterface.php) when files are not stored in the local filesystem.

Typical options:

- S3/MinIO/Cloud object storage
- network file service
- internal binary storage

If stored files have public URLs, implement [PublicStoreInterface](../../../src/Store/PublicStoreInterface.php).

## Integration Pattern

1. Build `Storage` with your `RepositoryInterface` and `StoreInterface` implementations.
2. Register it via `StorageProvider::set(...)` if file objects call `getUrl()`, `getContent()`, or `getResource()` directly.
3. Use the regular package API: `add()`, `findById()`, `remove()`, `getUrl()`, `getContent()`, and `getResource()`.
