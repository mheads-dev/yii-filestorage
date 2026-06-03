<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store\FileSystem;

use Exception;
use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Mheads\Yii\Filestorage\Helper;
use Mheads\Yii\Filestorage\Store\AddFileResult;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\PathGeneratorInterface;
use Override;
use Psr\Http\Message\UploadedFileInterface;
use Random\RandomException;
use Yiisoft\Files\FileHelper;

use function dirname;
use function file_get_contents;
use function is_dir;
use function is_file;
use function realpath;
use function str_starts_with;
use function trim;

trait FileSystemStoreTrait
{
	abstract protected function getRootPath(): string;

	abstract protected function getPathGenerator(): PathGeneratorInterface;

	#[Override]
	public function addFile(
		UploadedFileInterface $uploadedFile,
		string                $groupName,
	): AddFileResult {
		$rootPath = $this->getRootPath();
		if(!is_dir($rootPath))
		{
			throw new AddException("Directory '{$rootPath}' does not exist");
		}

		$groupDirName = trim($groupName, '/');
		$fileName = Helper::sanitizeFileName($uploadedFile->getClientFilename() ?? 'unknown');

		$relativeDirPath = $this->generateDirectoryRelativePath(
			$groupDirName,
			$fileName,
		);

		$absoluteDirPath = $rootPath . '/' . $relativeDirPath;
		$relativeFilePath = $relativeDirPath . '/' . $fileName;

		$this->ensureDirectory($absoluteDirPath);

		try
		{
			$uploadedFile->moveTo($rootPath . '/' . $relativeFilePath);
		}
		catch(Exception $e)
		{
			throw new AddException("Filed to add file", 0, $e);
		}

		return new AddFileResult(
			relativePath: $relativeFilePath,
		);
	}

	#[Override]
	public function removeFile(FileInterface $file): void
	{
		$relativePath = $file->getRelativePath();
		if($relativePath === null)
		{
			throw new RemoveException("File has no relative path");
		}

		$filePath = $this->getAbsoluteFilePath($relativePath);
		$dirname = dirname($filePath);

		try
		{
			FileHelper::unlink($filePath);
		}
		catch(Exception $e)
		{
			throw new RemoveException("Filed to delete file", 0, $e);
		}

		// Silent delete Empty directory
		try
		{
			$this->removeEmptyParentDirectories($dirname);
		}
		catch(Exception)
		{
		}
	}

	protected function getAbsoluteFilePath(string $relativePath): string
	{
		return $this->getRootPath() . '/' . $relativePath;
	}

	#[Override]
	public function getFileContent(FileInterface $file): ?string
	{
		$relativePath = $file->getRelativePath();
		if($relativePath === null)
		{
			return null;
		}

		$filePath = $this->getAbsoluteFilePath($relativePath);
		if(!is_file($filePath))
		{
			return null;
		}

		$content = file_get_contents($filePath);
		return $content === false ? null : $content;
	}

	#[Override]
	public function getFileResource(FileInterface $file)
	{
		$relativePath = $file->getRelativePath();
		if($relativePath === null)
		{
			return null;
		}

		$path = $this->getAbsoluteFilePath($relativePath);
		if(!is_file($path))
		{
			return null;
		}
		return FileHelper::openFile($path, 'r');
	}

	/**
	 * @throws RandomException
	 */
	protected function generateDirectoryRelativePath(
		string $groupDirName,
		string $fileName,
	): string {
		return $this->getPathGenerator()->generate(
			$this->getRootPath(),
			$groupDirName,
			$fileName,
		);
	}

	/**
	 * @throws AddException
	 */
	protected function ensureDirectory(string $path): void
	{
		try
		{
			FileHelper::ensureDirectory($path);
		}
		catch(Exception $e)
		{
			throw new AddException(
				"Failed to create directory \"$path\": " . $e->getMessage(),
				0,
				$e,
			);
		}
	}

	/**
	 * Removes empty directories from leaf to root (exclusive).
	 *
	 * Silent by design: cleanup must not break successful file removal.
	 *
	 * @throws Exception
	 */
	private function removeEmptyParentDirectories(string $startDirectory): void
	{
		$rootReal = realpath($this->getRootPath());
		if($rootReal === false)
		{
			return;
		}

		$current = $startDirectory;
		while(true)
		{
			$currentReal = realpath($current);
			if($currentReal === false)
			{
				return;
			}

			// Never delete root directory itself.
			if($currentReal === $rootReal)
			{
				return;
			}

			// Safety: delete only inside current store root.
			if(!str_starts_with($currentReal . '/', $rootReal . '/'))
			{
				return;
			}

			if(!FileHelper::isEmptyDirectory($currentReal))
			{
				return;
			}

			FileHelper::removeDirectory($currentReal);
			$current = dirname($currentReal);
		}
	}
}
