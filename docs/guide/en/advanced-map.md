# Advanced documentation map

## Storage topology

- [Multiple stores and storages](multiple-stores-and-storages.md) - one storage with many stores, or several named storage instances.
- [Custom file class](custom-file-class.md) - binding file objects to a named storage.

## ActiveRecord recipes

- [Two file fields in one AR model](two-file-fields.md) - configuring separate `#[FileUpload]` attributes for separate file-id fields.
- [AR file upload lifecycle](active-record-file-upload.md) - upload, cleanup, delete, and shutdown cleanup behavior.

## Extension points

- [Custom repository and store adapters](custom-adapters.md) - implementing `RepositoryInterface`, `StoreInterface`, and `PublicStoreInterface`.
- [Repositories: DB and ActiveRecord](repositories.md) - choosing the metadata backend.
- [Stores: public and private](stores.md) - choosing the physical storage policy.

## Operations

- [Migrations](migrations.md) - file metadata table setup.
- [Errors and exceptions](errors.md) - exception map and recommended handling.
- [Internals guide](../../internals.md) - local QA tooling.
