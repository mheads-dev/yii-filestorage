<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store\FileSystem;

use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\PathGeneratorInterface;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\RandomPathGenerator;
use Mheads\Yii\Filestorage\Store\PublicStoreInterface;
use Override;

use function ltrim;
use function strlen;

final class PublicFileSystemStore implements PublicStoreInterface
{
	use FileSystemStoreTrait;
	private readonly PathGeneratorInterface $pathGenerator;

	/**
	 * @param string $name Store name
	 * @param string $path Path to directory for files in public folder accessible from WEB
	 * @param string $baseUrl Base URL for public file
	 * @throws InvalidConfigException
	 */
	public function __construct(
		private readonly string $name,
		private readonly string $path,
		private readonly string $baseUrl,
		?PathGeneratorInterface $pathGenerator = null,
	) {
		if(strlen($path) === 0)
		{
			throw new InvalidConfigException("Path is required");
		}

		$this->pathGenerator = $pathGenerator ?? new RandomPathGenerator();
	}

	#[Override]
	public function getFileUrl(FileInterface $file): string
	{
		$relativePath = $file->getRelativePath();
		if ($relativePath === null)
		{
			throw new InvalidConfigException("File has no relative path");
		}

		return $this->baseUrl
			. '/'
			. ltrim($relativePath, '/');
	}

	#[Override]
	protected function getRootPath(): string
	{
		return $this->path;
	}

	#[Override]
	protected function getPathGenerator(): PathGeneratorInterface
	{
		return $this->pathGenerator;
	}

	#[Override]
	public function getName(): string
	{
		return $this->name;
	}
}
