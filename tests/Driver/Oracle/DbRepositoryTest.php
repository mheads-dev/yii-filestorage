<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Tests\Driver\Oracle;

use Mheads\Yii\Filestorage\Tests\Driver\Common\DbRepositoryTestCase;
use Mheads\Yii\Filestorage\Tests\Support\OracleHelper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Yiisoft\Db\Connection\ConnectionInterface;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
final class DbRepositoryTest extends DbRepositoryTestCase
{
	protected static function createConnection(): ConnectionInterface
	{
		return (new OracleHelper())->createConnection();
	}
}
