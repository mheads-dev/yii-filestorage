<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Psr\Http\Message\UploadedFileInterface;

interface StoreInterface
{
	/**
	 * Add file to store
	 * @throws AddException|InvalidConfigException
	 */
	public function addFile(
		UploadedFileInterface $uploadedFile,
		string                $groupName,
	): AddFileResult;

	/**
	 * Remove file from store
	 * @throws InvalidConfigException|RemoveException
	 */
	public function removeFile(FileInterface $file): void;

	/**
	 * Get file content
	 * @throws InvalidConfigException
	 */
	public function getFileContent(FileInterface $file): ?string;

	/**
	 * Get file resource
	 * @return resource|null The file pointer resource
	 * @throws InvalidConfigException
	 */
	public function getFileResource(FileInterface $file);

	/**
	 * Get store name
	 */
	public function getName(): string;
}
