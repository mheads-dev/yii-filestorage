<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;

interface PublicStoreInterface extends StoreInterface
{
	/**
	 * Get file public URL
	 * @throws InvalidConfigException
	 */
	public function getFileUrl(FileInterface $file): string;
}