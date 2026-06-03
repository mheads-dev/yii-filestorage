# Repositories: DB and ActiveRecord

Repositories handle file metadata.

## DbRepository

`DbRepository` stores metadata with `yiisoft/db` in the `mh_filestorage_file` table.

```php
use Mheads\Yii\Filestorage\Repository\DbRepository;

$repository = new DbRepository($db);
```

Use this when you need a simple DB-backed metadata repository without depending on ActiveRecord models.

## ActiveRecordRepository

`ActiveRecordRepository` stores metadata through `ArFile` or your custom `ArFile` class.

```php
use Mheads\Yii\Filestorage\ActiveRecord\ArFile;
use Mheads\Yii\Filestorage\Repository\ActiveRecordRepository;

$repository = new ActiveRecordRepository(ArFile::class);
```

Use this when your application works with `ArFile` relations and ActiveRecord queries.

## Custom repositories

For non-standard metadata storage, implement `RepositoryInterface`. See [Custom repository and store adapters](custom-adapters.md).
