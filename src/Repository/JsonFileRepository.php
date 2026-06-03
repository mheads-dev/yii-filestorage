<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Repository;

use DateTimeImmutable;
use Exception;
use Mheads\Yii\Filestorage\Entity\File;
use Mheads\Yii\Filestorage\Entity\FileInterface;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Mheads\Yii\Filestorage\Helper;
use Override;
use Psr\Http\Message\UploadedFileInterface;

use function array_values;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function json_decode;
use function json_encode;
use function rename;
use function tempnam;
use function unlink;

/**
 * Lightweight JSON repository for local/manual facade testing.
 * Not intended for production use.
 *
 * @psalm-type FileRow = array{
 *     id: int|string|null,
 *     storeName: string,
 *     externalId: ?string,
 *     groupName: string,
 *     relativePath: ?string,
 *     originalName: string,
 *     height: ?int,
 *     width: ?int,
 *     fileSize: ?int,
 *     contentType: ?string,
 *     description: ?string,
 *     createdAt: ?string,
 *     updatedAt: ?string
 * }
 * @psalm-type RepoData = array{
 *     files: list<FileRow>,
 *     lastId: int
 * }
 *
 * @internal
 */
final class JsonFileRepository implements RepositoryInterface
{
	/** @var RepoData */
	private array $data = ['files' => [], 'lastId' => 0];
	private int   $lastId = 0;

	/**
	 * @param class-string<FileInterface> $fileClass
	 * @throws AddException|InvalidConfigException
	 */
	public function __construct(
		private readonly string $jsonFilePath,
		private readonly string $fileClass = File::class,
	) {
		if(!is_a($this->fileClass, FileInterface::class, true))
		{
			throw new InvalidConfigException(
				sprintf(
					'File class must implement %s, %s given',
					FileInterface::class,
					$this->fileClass,
				),
			);
		}

		$this->loadData();
	}

	/**
	 * @throws AddException|InvalidConfigException
	 */
	private function loadData(): void
	{
		if(!file_exists($this->jsonFilePath))
		{
			$this->data = ['files' => [], 'lastId' => 0];
			$this->saveData();
			return;
		}

		$content = file_get_contents($this->jsonFilePath);
		if($content === false)
		{
			throw new InvalidConfigException("Cannot read JSON file: {$this->jsonFilePath}");
		}

		$data = json_decode($content, true);
		if(
			!is_array($data)
			|| !isset($data['files'], $data['lastId'])
			|| !is_array($data['files'])
			|| !is_int($data['lastId'])
		) {
			throw new InvalidConfigException("Invalid JSON in file: {$this->jsonFilePath}");
		}

		$rows = [];
		foreach($data['files'] as $row)
		{
			if(!is_array($row))
			{
				continue;
			}

			$rows[] = $this->normalizeRow($row);
		}

		$this->data = [
			'files'  => $rows,
			'lastId' => $data['lastId'],
		];
		$this->lastId = $data['lastId'];
	}

	/**
	 * @throws AddException
	 */
	private function saveData(): void
	{
		$this->data['lastId'] = $this->lastId;
		$json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if($json === false)
		{
			throw new AddException("Cannot encode data to JSON");
		}

		// Write via temp file and atomic rename to avoid partial/corrupted main JSON on crash.
		$tempFilePath = tempnam(dirname($this->jsonFilePath), 'filestorage_');
		if($tempFilePath === false)
		{
			throw new AddException("Cannot create temp file for JSON repository");
		}

		$result = file_put_contents($tempFilePath, $json, LOCK_EX);
		if($result === false)
		{
			@unlink($tempFilePath);
			throw new AddException("Cannot write to temp JSON file: {$tempFilePath}");
		}

		if(!rename($tempFilePath, $this->jsonFilePath))
		{
			@unlink($tempFilePath);
			throw new AddException("Cannot move temp JSON file to: {$this->jsonFilePath}");
		}
	}

	#[Override]
	public function findById(int|string $id): ?FileInterface
	{
		$needle = (string)$id;
		foreach($this->data['files'] as $fileData)
		{
			if((string)$fileData['id'] === $needle)
			{
				return $this->createFileFromArray($fileData);
			}
		}
		return null;
	}

	/**
	 * @throws AddException
	 */
	#[Override]
	public function add(FileInterface $file): int|string
	{
		try
		{
			if($file->getId() === null)
			{
				// New file
				$this->lastId++;
				$file->assignId($this->lastId);
				$fileData = $this->convertFileToArray($file);
				$this->data['files'][] = $fileData;
			}
			else
			{
				throw new AddException("File already added");
			}

			$this->saveData();
			$fileId = $file->getId();
			if($fileId === null)
			{
				throw new AddException('File ID was not assigned by repository');
			}

			return $fileId;
		}
		catch(Exception $e)
		{
			throw new AddException("Failed to save file: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * @throws RemoveException
	 */
	#[Override]
	public function remove(FileInterface $file): void
	{
		try
		{
			if($file->getId() === null)
			{
				throw new RemoveException("Cannot remove file without ID");
			}

			$id = $file->getId();
			$found = false;
			foreach($this->data['files'] as $key => $fileData)
			{
				if($fileData['id'] === $id)
				{
					unset($this->data['files'][$key]);
					$found = true;
					break;
				}
			}

			if(!$found)
			{
				throw new RemoveException("File with id {$id} not found");
			}

			$this->data['files'] = array_values($this->data['files']);
			$this->saveData();
		}
		catch(Exception $e)
		{
			throw new RemoveException("Failed to remove file: " . $e->getMessage(), 0, $e);
		}
	}

	#[Override]
	public function createFromUploadedFile(
		UploadedFileInterface $uploadedFile,
		string                $groupName,
		string                $storeName,
		?string               $description = null,
	): FileInterface {
		return Helper::createFileFromUploadedFile(
			$uploadedFile,
			$groupName,
			$storeName,
			$this->fileClass,
			$description,
		);
	}

	/**
	 * @return FileRow
	 */
	private function convertFileToArray(FileInterface $file): array
	{
		return [
			'id'           => $file->getId(),
			'storeName'    => $file->getStoreName(),
			'externalId'   => $file->getExternalId(),
			'groupName'    => $file->getGroupName(),
			'relativePath' => $file->getRelativePath(),
			'originalName' => $file->getOriginalName(),
			'height'       => $file->getHeight(),
			'width'        => $file->getWidth(),
			'fileSize'     => $file->getFileSize(),
			'contentType'  => $file->getContentType(),
			'description'  => $file->getDescription(),
			'createdAt'    => $file->getCreatedAt()?->format('Y-m-d H:i:s'),
			'updatedAt'    => $file->getUpdatedAt()?->format('Y-m-d H:i:s'),
		];
	}

	/**
	 * @param FileRow $data
	 */
	private function createFileFromArray(array $data): FileInterface
	{
		/** @var class-string<FileInterface> $fileClass */
		$fileClass = $this->fileClass;
		$file = new $fileClass();

		if($data['id'] !== null)
		{
			$file->assignId($data['id']);
		}

		$file->setStoreName($data['storeName']);
		$file->setExternalId($data['externalId'] ?? null);
		$file->setGroupName($data['groupName']);
		$file->setRelativePath($data['relativePath'] ?? null);
		$file->setOriginalName($data['originalName']);
		$file->setHeight($data['height'] ?? null);
		$file->setWidth($data['width'] ?? null);
		$file->setFileSize($data['fileSize'] ?? null);
		$file->setContentType($data['contentType'] ?? null);
		$file->setDescription($data['description'] ?? null);

		if(!empty($data['createdAt']))
		{
			$createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['createdAt']);
			$file->setCreatedAt($createdAt === false ? null : $createdAt);
		}
		if(!empty($data['updatedAt']))
		{
			$updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['updatedAt']);
			$file->setUpdatedAt($updatedAt === false ? null : $updatedAt);
		}

		return $file;
	}

	/**
	 * @param array<array-key, mixed> $row
	 * @return FileRow
	 */
	private function normalizeRow(array $row): array
	{
		return [
			'id'           => isset($row['id']) ? (int)$row['id'] : null,
			'storeName'    => (string)($row['storeName'] ?? ''),
			'externalId'   => isset($row['externalId']) ? (string)$row['externalId'] : null,
			'groupName'    => (string)($row['groupName'] ?? ''),
			'relativePath' => isset($row['relativePath']) ? (string)$row['relativePath'] : null,
			'originalName' => (string)($row['originalName'] ?? ''),
			'height'       => isset($row['height']) ? (int)$row['height'] : null,
			'width'        => isset($row['width']) ? (int)$row['width'] : null,
			'fileSize'     => isset($row['fileSize']) ? (int)$row['fileSize'] : null,
			'contentType'  => isset($row['contentType']) ? (string)$row['contentType'] : null,
			'description'  => isset($row['description']) ? (string)$row['description'] : null,
			'createdAt'    => isset($row['createdAt']) ? (string)$row['createdAt'] : null,
			'updatedAt'    => isset($row['updatedAt']) ? (string)$row['updatedAt'] : null,
		];
	}
}
