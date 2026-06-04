# Errors and exceptions

Main package exceptions:

- `Mheads\Yii\Filestorage\Exception\AddException`
- `Mheads\Yii\Filestorage\Exception\FindException`
- `Mheads\Yii\Filestorage\Exception\RemoveException`
- `Mheads\Yii\Filestorage\Exception\InvalidConfigException`

Recommended handling:

- catch `AddException` in upload use-cases
- catch `FindException`/`RemoveException` in read/delete use-cases
- treat `InvalidConfigException` as configuration error and fail fast on startup
