# Custom repository and store adapters

Package supports replacing built-in adapters with your own implementations.

## Repository (Metadata)

Implement [RepositoryInterface](../../../src/Repository/RepositoryInterface.php) when file metadata is not stored in the package's standard DB table.

Typical options:

- dedicated table/schema
- Redis;
- external HTTP/gRPC service.

## Store (Physical Files)

Implement [StoreInterface](../../../src/Store/StoreInterface.php) when files are not stored in local FS.

Typical options:

- S3/MinIO/Cloud object storage;
- network file service;
- internal binary storage.

If store is public (URL needed), implement [PublicStoreInterface](../../../src/Store/PublicStoreInterface.php).

Roadmap: ready adapters for popular backends are planned as separate packages.

## Integration Pattern

1. Build `Storage` with your `RepositoryInterface` and your `StoreInterface`.
2. Register it via `StorageProvider::set(...)`.
3. Use regular package API (`add/find/remove/getUrl/getContent/getResource`).
