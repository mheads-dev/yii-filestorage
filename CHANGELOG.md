# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-06-05

### Added

- First stable production release of the core package.
- Added stable core API for file upload, lookup, removal, URL resolution, content reading, and resource access.
- Added public and private filesystem stores.
- Added `RepositoryInterface` for metadata storage implementations.
- Added `DemoRepository` for examples and local/manual testing.
- Added documentation for adapters, custom repositories, custom stores, named storages, and private file serving.

### Changed

- Finalized the core package as a lightweight storage facade with filesystem stores and repository/store contracts.
- Documented production metadata storage through adapter packages:
  - [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db)
  - [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record)

### Notes

- `DemoRepository` is not intended for production metadata storage. Use a supported adapter or implement `RepositoryInterface` for production projects.

## [1.0.0-beta2] - 2026-06-04

### Changed

- Split DB and ActiveRecord integrations out of the core package API.
- Reduced core Composer dependencies to filesystem, string, and PSR-7 uploaded-file contracts.
- Replaced `JsonFileRepository` with `DemoRepository` for examples and manual testing.
- Updated examples to use `DemoRepository` instead of DB-backed repositories.
- Simplified the root Docker/Makefile workflow to a PHP-only core test environment.
- Reworked documentation for the core package and linked adapter repositories:
  - [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db)
  - [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record)

### Removed

- Removed `yiisoft/db`, DB driver packages, `yiisoft/db-migration`, `yiisoft/active-record`, and `yiisoft/event-dispatcher` from the core package dependencies.
- Removed DB repository, ActiveRecord repository, ActiveRecord file model, upload lifecycle handler, migrations, DB/AR examples, and DB/AR test suites from the core package.
- Removed DB service infrastructure from the root Docker setup.
- Removed DB/AR-specific guide pages from the core documentation.

### Added

- Added `DemoRepository` as a lightweight local repository for examples and manual facade testing.
- Added explicit adapter information to the root README.
