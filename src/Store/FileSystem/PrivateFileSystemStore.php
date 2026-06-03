<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store\FileSystem;

use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\PathGeneratorInterface;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\RandomPathGenerator;
use Mheads\Yii\Filestorage\Store\StoreInterface;
use Override;

use function strlen;

final class PrivateFileSystemStore implements StoreInterface
{
	use FileSystemStoreTrait;
	private readonly PathGeneratorInterface $pathGenerator;

	/**
	 * @param string $name Store name
	 * @param string $path Path to directory for files in private folder not accessible from WEB
	 * @throws InvalidConfigException
	 */
	public function __construct(
		private readonly string $name,
		private readonly string $path,
		?PathGeneratorInterface $pathGenerator = null,
	) {
		if(strlen($path) === 0)
		{
			throw new InvalidConfigException("Path is required");
		}

		$this->pathGenerator = $pathGenerator ?? new RandomPathGenerator();
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
