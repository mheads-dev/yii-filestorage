<?php

namespace Mheads\Yii\Filestorage\Repository;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\FindException;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Psr\Http\Message\UploadedFileInterface;

interface RepositoryInterface
{
	/**
	 * @throws FindException
	 */
	public function findById(int|string $id): ?FileInterface;

	/**
	 * @throws AddException|InvalidConfigException
	 */
	public function add(FileInterface $file): int|string;

	/**
	 * @throws InvalidConfigException|RemoveException
	 */
	public function remove(FileInterface $file): void;

	public function createFromUploadedFile(
		UploadedFileInterface $uploadedFile,
		string                $groupName,
		string                $storeName,
		?string               $description = null,
	): FileInterface;
}
