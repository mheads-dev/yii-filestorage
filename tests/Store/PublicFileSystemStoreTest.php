<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Tests\Store;

use Exception;
use Mheads\Yii\Filestorage\Entity\File;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Mheads\Yii\Filestorage\Store\FileSystem\Path\PathGeneratorInterface;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;
use Mheads\Yii\Filestorage\Tests\Support\CreateUploadedFileMockTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;
use Yiisoft\Files\FileHelper;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class PublicFileSystemStoreTest extends TestCase
{
	use CreateUploadedFileMockTrait;

	private string                $tempDir;
	private string                $path;
	private PublicFileSystemStore $store;

	protected function setUp(): void
	{
		parent::setUp();

		// Создаем временную директорию для тестов
		$this->tempDir = sys_get_temp_dir() . '/filestorage_test_' . uniqid();
		$this->path = $this->tempDir . '/public';

		// Создаем директорию
		FileHelper::ensureDirectory($this->path);

		// Создаем экземпляр хранилища
		$this->store = new PublicFileSystemStore(
			name: 'test-store',
			path: $this->path,
			baseUrl: 'https://example.com/files',
		);
	}

	protected function tearDown(): void
	{
		// Удаляем временную директорию
		if(is_dir($this->tempDir))
		{
			FileHelper::removeDirectory($this->tempDir);
		}

		parent::tearDown();
	}

	public function testGetName(): void
	{
		self::assertSame('test-store', $this->store->getName());
	}

	public function testAddFile(): void
	{
		// Создаем mock UploadedFile
		$uploadedFile = $this->createUploadedFileMock(
			'test.txt',
			'Hello, World!',
		);

		// Добавляем файл в хранилище
		$result = $this->store->addFile($uploadedFile, 'test-group');

		// Проверяем результат
		self::assertNotNull($result->relativePath);
		self::assertStringContainsString('test-group', $result->relativePath);
		self::assertNull($result->externalId);

		// Проверяем, что файл создан
		$filePath = $this->path . '/' . $result->relativePath;
		self::assertFileExists($filePath);
		self::assertSame('Hello, World!', file_get_contents($filePath));
	}

	public function testAddFileWithSpecialCharactersInFilename(): void
	{
		$uploadedFile = $this->createUploadedFileMock(
			'test file with spaces & special chars.txt',
			'Content',
		);

		$result = $this->store->addFile($uploadedFile, 'test-group');

		self::assertNotNull($result->relativePath);
		$filePath = $this->path . '/' . $result->relativePath;
		self::assertFileExists($filePath);

		// Проверяем, что имя файла было санитизировано
		$fileName = basename($result->relativePath);
		self::assertMatchesRegularExpression('/^[a-zA-Z0-9\-_\.]+$/', $fileName);
	}

	public function testConstructorThrowsExceptionWhenPathEmpty(): void
	{
		$this->expectException(InvalidConfigException::class);
		$this->expectExceptionMessage('Path is required');

		new PublicFileSystemStore(
			name: 'invalid-store',
			path: '',
			baseUrl: 'https://example.com/files',
		);
	}

	public function testAddFileThrowsExceptionWhenDirectoryCreationFails(): void
	{
		// Создаем хранилище с невалидным путем
		$store = new PublicFileSystemStore(
			name: 'test-store',
			path: '/invalid/path/that/does/not/exist',
			baseUrl: 'https://example.com/files',
		);

		$uploadedFile = $this->createUploadedFileMock('test.txt', 'Content');

		$this->expectException(AddException::class);
		$this->expectExceptionMessage("Directory '/invalid/path/that/does/not/exist' does not exist");

		$store->addFile($uploadedFile, 'test-group');
	}

	public function testAddFileThrowsExceptionWhenFileMoveFails(): void
	{
		$uploadedFile = $this->createMock(UploadedFileInterface::class);
		$uploadedFile->method('getClientFilename')->willReturn('test.txt');
		$uploadedFile->method('moveTo')->willThrowException(new Exception('Move failed'));

		$this->expectException(AddException::class);
		$this->expectExceptionMessage('Filed to add file');

		$this->store->addFile($uploadedFile, 'test-group');
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
		$file->setStoreName('test-store');
		$file->setGroupName('delete-group');
		$file->setRelativePath($result->relativePath);
		$file->setOriginalName('delete-test.txt');

		// Удаляем файл
		$this->store->removeFile($file);

		// Проверяем, что файл удален
		self::assertFileDoesNotExist($filePath);
	}

	public function testDeleteFileThrowsExceptionWhenFileDoesNotExist(): void
	{
		// Создаем объект File с несуществующим путем
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('non-existent');
		$file->setRelativePath('non-existent/file.txt');
		$file->setOriginalName('non-existent.txt');

		$this->expectException(RemoveException::class);
		$this->expectExceptionMessage('Filed to delete file');

		$this->store->removeFile($file);
	}

	public function testDeleteFileRemovesEmptyDirectory(): void
	{
		// Добавляем файл
		$uploadedFile = $this->createUploadedFileMock('single-file.txt', 'Content');
		$result = $this->store->addFile($uploadedFile, 'empty-dir-group');

		$filePath = $this->path . '/' . $result->relativePath;
		$dirPath = dirname($filePath);

		self::assertFileExists($filePath);
		self::assertDirectoryExists($dirPath);

		// Создаем объект File для удаления
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('empty-dir-group');
		$file->setRelativePath($result->relativePath);
		$file->setOriginalName('single-file.txt');

		// Удаляем файл
		$this->store->removeFile($file);

		// Проверяем, что файл и пустая директория удалены
		self::assertFileDoesNotExist($filePath);
		self::assertDirectoryDoesNotExist($dirPath);
	}

	public function testDeleteFileRemovesEmptyDirectoryChainUpToRoot(): void
	{
		$relativePath = 'deep/a/b/c/remove-me.txt';
		$filePath = $this->path . '/' . $relativePath;
		$leafDir = dirname($filePath);
		$levelC = $this->path . '/deep/a/b/c';
		$levelB = $this->path . '/deep/a/b';
		$levelA = $this->path . '/deep/a';
		$levelDeep = $this->path . '/deep';

		FileHelper::ensureDirectory($leafDir);
		file_put_contents($filePath, 'to delete');

		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('deep');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('remove-me.txt');

		$this->store->removeFile($file);

		self::assertFileDoesNotExist($filePath);
		self::assertDirectoryDoesNotExist($levelC);
		self::assertDirectoryDoesNotExist($levelB);
		self::assertDirectoryDoesNotExist($levelA);
		self::assertDirectoryDoesNotExist($levelDeep);
		self::assertDirectoryExists($this->path);
	}

	public function testGetFilePublicUrl(): void
	{
		$relativePath = 'test-group/abc/def/test.txt';

		// Создаем объект File
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('test-group');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('test.txt');

		$expectedUrl = 'https://example.com/files/' . $relativePath;

		self::assertSame($expectedUrl, $this->store->getFileUrl($file));
	}

	public function testGetFilePublicUrlWithLeadingSlash(): void
	{
		$relativePath = '/test-group/abc/def/test.txt';

		// Создаем объект File
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('test-group');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('test.txt');

		$expectedUrl = 'https://example.com/files/test-group/abc/def/test.txt';

		self::assertSame($expectedUrl, $this->store->getFileUrl($file));
	}

	public function testGetFileContent(): void
	{
		// Создаем тестовый файл
		$testContent = 'Test file content for reading';
		$relativePath = 'test-group/test.txt';

		$fullPath = $this->path . '/' . $relativePath;
		FileHelper::ensureDirectory(dirname($fullPath));
		file_put_contents($fullPath, $testContent);

		// Создаем объект File
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('test-group');
		$file->setRelativePath($relativePath);
		$file->setOriginalName('test.txt');

		// Получаем содержимое файла
		$content = $this->store->getFileContent($file);

		self::assertSame($testContent, $content);
	}

	public function testGetFileContentReturnsNullWhenFileDoesNotExist(): void
	{
		// Создаем объект File с несуществующим путем
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('non-existent');
		$file->setRelativePath('non-existent/file.txt');
		$file->setOriginalName('non-existent.txt');

		$content = $this->store->getFileContent($file);

		self::assertNull($content);
	}

	public function testGetFileResource(): void
	{
		// Создаем тестовый файл
		$testContent = 'Test file content for resource';
		$relativePath = 'test-group/resource.txt';

		$fullPath = $this->path . '/' . $relativePath;
		FileHelper::ensureDirectory(dirname($fullPath));
		file_put_contents($fullPath, $testContent);

		// Создаем объект File
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('test-group');
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

	public function testGetFileResourceReturnsNullWhenFileDoesNotExist(): void
	{
		// Создаем объект File с несуществующим путем
		$file = new File();
		$file->setStoreName('test-store');
		$file->setGroupName('non-existent');
		$file->setRelativePath('non-existent/file.txt');
		$file->setOriginalName('non-existent.txt');

		$resource = $this->store->getFileResource($file);

		self::assertNull($resource);
	}

	public function testGetAbsoluteFilePath(): void
	{
		$relativePath = 'test-group/file.txt';

		// Доступ к protected методу через рефлексию
		$reflection = new ReflectionClass($this->store);
		$method = $reflection->getMethod('getAbsoluteFilePath');

		$absolutePath = $method->invoke($this->store, $relativePath);

		self::assertSame($this->path . '/' . $relativePath, $absolutePath);
	}

	public function testGenerateDirectoryRelativePath(): void
	{
		// Доступ к protected методу через рефлексию
		$reflection = new ReflectionClass($this->store);
		$method = $reflection->getMethod('generateDirectoryRelativePath');

		$groupDirName = 'test-group';
		$fileName = 'test.txt';

		$result = $method->invoke($this->store, $groupDirName, $fileName);

		// Проверяем формат пути
		self::assertStringStartsWith($groupDirName . '/', $result);
		self::assertMatchesRegularExpression('/^test-group\/[a-zA-Z0-9]{3}$/', $result);
	}

	public function testAddFileUsesInjectedDirectoryPathGenerator(): void
	{
		$generator = new class implements PathGeneratorInterface {
			public function generate(
				string $rootPath,
				string $groupDirName,
				string $fileName,
			): string {
				return $groupDirName . '/fixed-dir';
			}
		};

		$store = new PublicFileSystemStore(
			name: 'test-store',
			path: $this->path,
			baseUrl: 'https://example.com/files',
			pathGenerator: $generator,
		);

		$uploadedFile = $this->createUploadedFileMock('custom.txt', 'Content');
		$result = $store->addFile($uploadedFile, 'custom-group');

		self::assertSame('custom-group/fixed-dir/custom.txt', $result->relativePath);
	}
}
