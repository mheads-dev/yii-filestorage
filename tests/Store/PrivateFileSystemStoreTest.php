<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Tests\Store;

use Mheads\Yii\Filestorage\Entity\File;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Store\FileSystem\PrivateFileSystemStore;
use Mheads\Yii\Filestorage\Tests\Support\CreateUploadedFileMockTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Yiisoft\Files\FileHelper;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class PrivateFileSystemStoreTest extends TestCase
{
	use CreateUploadedFileMockTrait;

	private string $tempDir;
	private string $path;
	private PrivateFileSystemStore $store;

	protected function setUp(): void
	{
		parent::setUp();

		// Создаем временную директорию для тестов
		$this->tempDir = sys_get_temp_dir() . '/filestorage_test_' . uniqid();
		$this->path = $this->tempDir . '/private';

		// Создаем директорию
		FileHelper::ensureDirectory($this->path);

		// Создаем экземпляр хранилища
		$this->store = new PrivateFileSystemStore(
			name: 'private-store',
			path: $this->path,
		);
	}

	protected function tearDown(): void
	{
		// Удаляем временную директорию
		if (is_dir($this->tempDir))
		{
			FileHelper::removeDirectory($this->tempDir);
		}

		parent::tearDown();
	}

	public function testGetName(): void
	{
		self::assertSame('private-store', $this->store->getName());
	}

	public function testAddFile(): void
	{
		// Создаем mock UploadedFile
		$uploadedFile = $this->createUploadedFileMock(
			'private.txt',
			'Private content',
		);

		// Добавляем файл в хранилище
		$result = $this->store->addFile($uploadedFile, 'private-group');

		// Проверяем результат
		self::assertNotNull($result->relativePath);
		self::assertStringContainsString('private-group', $result->relativePath);
		self::assertNull($result->externalId);

		// Проверяем, что файл создан
		$filePath = $this->path . '/' . $result->relativePath;
		self::assertFileExists($filePath);
		self::assertSame('Private content', file_get_contents($filePath));
	}

	public function testConstructorThrowsExceptionWhenPathEmpty(): void
	{
		$this->expectException(InvalidConfigException::class);
		$this->expectExceptionMessage('Path is required');

		new PrivateFileSystemStore(
			name: 'invalid-store',
			path: '',
		);
	}

	public function testDeleteFile(): void
	{
		// Сначала добавляем файл
		$uploadedFile = $this->createUploadedFileMock('delete-test.txt', 'To be deleted');
		$result = $this->store->addFile($uploadedFile, 'delete-group');

		$filePath = $this->path . '/' . $result->relativePath;
		self::assertFileExists($filePath);

		// Создаем объект File для удаления
		$file = new File();
		$file->setStoreName('private-store');
		$file->setGroupName('delete-group');
		$file->setRelativePath($result->relativePath);
		$file->setOriginalName('delete-test.txt');

		// Удаляем файл
		$this->store->removeFile($file);

		// Проверяем, что файл удален
		self::assertFileDoesNotExist($filePath);
	}

	public function testGetFileContent(): void
	{
		// Создаем тестовый файл
		$testContent = 'Private file content';
		$relativePath = 'private-group/secret.txt';

		$fullPath = $this->path . '/' . $relativePath;
		FileHelper::ensureDirectory(dirname($fullPath));
		file_put_contents($fullPath, $testContent);

		// Создаем объект File
		$file = new File();
		$file->setStoreName('private-store');
		$file->setGroupName('private-group');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('secret.txt');

		// Получаем содержимое файла
		$content = $this->store->getFileContent($file);

		self::assertSame($testContent, $content);
	}

	public function testGetFileResource(): void
	{
		// Создаем тестовый файл
		$testContent = 'Private file content for resource';
		$relativePath = 'private-group/resource.txt';

		$fullPath = $this->path . '/' . $relativePath;
		FileHelper::ensureDirectory(dirname($fullPath));
		file_put_contents($fullPath, $testContent);

		// Создаем объект File
		$file = new File();
		$file->setStoreName('private-store');
		$file->setGroupName('private-group');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('resource.txt');

		// Получаем ресурс файла
		$resource = $this->store->getFileResource($file);

		self::assertIsResource($resource);
		self::assertSame('stream', get_resource_type($resource));

		// Читаем из ресурса
		fseek($resource, 0);
		$content = fread($resource, strlen($testContent));
		self::assertSame($testContent, $content);

		fclose($resource);
	}
}