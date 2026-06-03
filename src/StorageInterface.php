<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\FindException;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Psr\Http\Message\UploadedFileInterface;

interface StorageInterface
{
	/**
	 * Find file by id via configured repository
	 * @throws FindException
	 */
	public function findById(int|string $id): ?FileInterface;

	/**
	 * Remove file by id.
	 *
	 * Returns `false` when file not found.
	 *
	 * @throws FindException|InvalidConfigException|RemoveException
	 */
	public function removeById(int|string $id): bool;

	/**
	 * Add file from uploaded file
	 * @throws AddException|InvalidConfigException
	 */
	public function add(
		UploadedFileInterface $uploadedFile,
		?string               $groupName = null,
		?string               $storeName = null,
		?string               $description = null,
	): FileInterface;

	/**
	 * Remove a file from storage
	 * @throws InvalidConfigException|RemoveException
	 */
	public function remove(FileInterface $file): void;

	/**
	 * Get file URL (for public files)
	 * @throws InvalidConfigException
	 */
	public function getUrl(FileInterface $file): ?string;

	/**
	 * Get file content
	 * @throws InvalidConfigException
	 */
	public function getContent(FileInterface $file): ?string;

	/**
	 * Get file resource
	 * @return resource|null
	 * @throws InvalidConfigException
	 */
	public function getResource(FileInterface $file);
}
