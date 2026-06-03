<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Tests\Driver\Mysql;

use Mheads\Yii\Filestorage\Tests\Driver\Common\ActiveRecordRepositoryTestCase;
use Mheads\Yii\Filestorage\Tests\Support\MysqlHelper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Yiisoft\Db\Connection\ConnectionInterface;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
final class ActiveRecordRepositoryTest extends ActiveRecordRepositoryTestCase
{
	protected static function createConnection(): ConnectionInterface
	{
		return (new MysqlHelper())->createConnection();
	}
}
