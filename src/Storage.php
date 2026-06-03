<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Mheads\Yii\Filestorage\Repository\RepositoryInterface;
use Mheads\Yii\Filestorage\Store\PublicStoreInterface;
use Mheads\Yii\Filestorage\Store\StoreInterface;
use Override;
use Psr\Http\Message\UploadedFileInterface;
use Throwable;

final class Storage implements StorageInterface
{
	/**
	 * @var array<string, PublicStoreInterface|StoreInterface>
	 */
	private array $stores = [];

	public const string DEFAULT_STORE_NAME = 'upload';
	public const string DEFAULT_GROUP_NAME = 'common';

	/**
	 * @param array<PublicStoreInterface|StoreInterface> $stores
	 */
	public function __construct(
		private readonly RepositoryInterface $repository,
		array                                $stores = [],
		private readonly string              $defaultStoreName = self::DEFAULT_STORE_NAME,
		private readonly string              $defaultGroupName = self::DEFAULT_GROUP_NAME,
		private readonly bool                $silentRemove = true,
	) {
		foreach($stores as $store)
		{
			$this->addStore($store);
		}
	}

	private function addStore(StoreInterface|PublicStoreInterface $store): void
	{
		$this->stores[$store->getName()] = $store;
	}

	#[Override]
	public function findById(int|string $id): ?FileInterface
	{
		return $this->repository->findById($id);
	}

	#[Override]
	public function removeById(int|string $id): bool
	{
		$file = $this->findById($id);
		if($file === null)
		{
			return false;
		}

		$this->remove($file);
		return true;
	}

	#[Override]
	public function add(
		UploadedFileInterface $uploadedFile,
		?string               $groupName = null,
		?string               $storeName = null,
		?string               $description = null,
	): FileInterface {
		$storeName ??= $this->defaultStoreName;
		$groupName ??= $this->defaultGroupName;

		$file = $this->repository->createFromUploadedFile(
			$uploadedFile,
			$groupName,
			$storeName,
			$description,
		);
		$store = $this->getStore($storeName);

		$storeResult = $store->addFile($uploadedFile, $groupName);
		$file->setRelativePath($storeResult->relativePath);
		$file->setExternalId($storeResult->externalId);

		try
		{
			$this->repository->add($file);
		}
		catch(Throwable $e)
		{
			try
			{
				$store->removeFile($file);
			}
			catch(Throwable)
			{
			}
			throw $e;
		}

		return $file;
	}

	#[Override]
	public function remove(FileInterface $file): void
	{
		if($this->silentRemove)
		{
			try
			{
				$this->removeInternal($file);
			}
			catch(Throwable)
			{
			}
		}
		else
		{
			$this->removeInternal($file);
		}
	}

	/**
	 * @throws InvalidConfigException
	 * @throws RemoveException
	 */
	private function removeInternal(FileInterface $file): void
	{
		$store = $this->getStore($file->getStoreName());
		$store->removeFile($file);
		$this->repository->remove($file);
	}

	#[Override]
	public function getUrl(FileInterface $file): ?string
	{
		$store = $this->getStore($file->getStoreName());
		if(!$store instanceof PublicStoreInterface)
		{
			// Private storage does not support public URLs
			return null;
		}

		return $store->getFileUrl($file);
	}

	#[Override]
	public function getContent(FileInterface $file): ?string
	{
		$store = $this->getStore($file->getStoreName());
		return $store->getFileContent($file);
	}

	#[Override]
	public function getResource(FileInterface $file)
	{
		$store = $this->getStore($file->getStoreName());
		return $store->getFileResource($file);
	}

	/**
	 * @throws InvalidConfigException
	 */
	private function getStore(string $storeName): StoreInterface
	{
		if(!isset($this->stores[$storeName]))
		{
			throw new InvalidConfigException("Store \"{$storeName}\" not found");
		}

		return $this->stores[$storeName];
	}

	public function getRepository(): RepositoryInterface
	{
		return $this->repository;
	}
}
