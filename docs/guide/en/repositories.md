# Repositories

Repositories handle file metadata through `RepositoryInterface`.

## RepositoryInterface

Implement `RepositoryInterface` when file metadata is stored in a database, external service, project model, or another backend.

The repository is responsible for:

- creating a file entity from an uploaded file
- saving metadata and assigning an id
- finding metadata by id
- removing metadata

## DemoRepository

`DemoRepository` is a lightweight local implementation for examples and manual facade testing.
It is not intended for production metadata storage.

```php
use Mheads\Yii\Filestorage\Repository\DemoRepository;

$repository = new DemoRepository('/app/runtime/demo-filestorage.json');
```

## Adapter Packages

DB and ActiveRecord implementations are provided by separate adapter packages:

- [`mheads/yii-filestorage-db`](https://github.com/mheads-dev/yii-filestorage-db)
- [`mheads/yii-filestorage-active-record`](https://github.com/mheads-dev/yii-filestorage-active-record)

Until an adapter package is installed, production projects should provide their own `RepositoryInterface` implementation.

See [Custom repository and store adapters](custom-adapters.md).
