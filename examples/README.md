# Examples

The scripts in this directory are reference/demo scripts for this library.
In real projects, adapt them to your runtime: paths, bootstrap, and launch method.

## Example Map

| Scenario | File |
| --- | --- |
| Public store + demo repository | [01-public-store.php](01-public-store.php) |
| Private store + demo repository | [02-private-store.php](02-private-store.php) |
| Multiple stores in one storage (public+private sample) | [03-public-and-private.php](03-public-and-private.php) |

## Preparation

1. Helper [support/UploadedFileFactory.php](support/UploadedFileFactory.php) requires `httpsoft/http-message`.
2. Create file storage directories or keep directory creation from samples.

## How To Run In Your Project

1. Copy `examples` folder into your project if needed.
2. Run script via your PHP launch flow: CLI/container/build scripts.

Important: samples are not intended to be copied into production unchanged.

## Notes

- Samples demonstrate API and typical lifecycle, not production architecture.
- `DemoRepository` is for local/manual examples, not production metadata storage.
- If you call `getUrl()/getContent()/getResource()` on file objects, register storage in `StorageProvider`.
