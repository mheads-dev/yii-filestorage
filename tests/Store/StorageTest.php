<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Tests\Store;

use Mheads\Yii\Filestorage\Entity\File;
use Mheads\Yii\Filestorage\Exception\AddException;
use Mheads\Yii\Filestorage\Exception\RemoveException;
use Mheads\Yii\Filestorage\Repository\RepositoryInterface;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\Store\AddFileResult;
use Mheads\Yii\Filestorage\Store\StoreInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
final class StorageTest extends TestCase
{
	public function testAddRollsBackStoreWhenRepositoryAddFails(): void
	{
		$uploadedFile = $this->createMock(UploadedFileInterface::class);

		$file = new File();
		$file->setStoreName('upload');
		$file->setGroupName('group');
		$file->setOriginalName('test.txt');

		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::once())
			->method('createFromUploadedFile')
			->with($uploadedFile, 'group', 'upload', null)
			->willReturn($file);
		$repository->expects(self::once())
			->method('add')
			->with($file)
			->willThrowException(new AddException('repository add failed'));

		$store = $this->createMock(StoreInterface::class);
		$store->method('getName')->willReturn('upload');
		$store->expects(self::once())
			->method('addFile')
			->with($uploadedFile, 'group')
			->willReturn(new AddFileResult('group/abc/test.txt'));
		$store->expects(self::once())
			->method('removeFile')
			->with($file);

		$storage = new Storage($repository, [$store], silentRemove: false);

		$this->expectException(AddException::class);
		$this->expectExceptionMessage('repository add failed');
		$storage->add($uploadedFile, 'group', 'upload');
	}

	public function testFindByIdDelegatesToRepository(): void
	{
		$file = new File();
		$file->assignId(5);
		$file->setStoreName('upload');
		$file->setGroupName('group');
		$file->setOriginalName('found.txt');

		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::once())
			->method('findById')
			->with(5)
			->willReturn($file);

		$storage = new Storage($repository, []);
		self::assertSame($file, $storage->findById(5));
	}

	public function testRemoveByIdReturnsFalseWhenFileNotFound(): void
	{
		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::once())
			->method('findById')
			->with(404)
			->willReturn(null);
		$repository->expects(self::never())->method('remove');

		$storage = new Storage($repository, []);
		self::assertFalse($storage->removeById(404));
	}

	public function testRemoveByIdReturnsTrueWhenFileFound(): void
	{
		$file = new File();
		$file->assignId(7);
		$file->setStoreName('upload');
		$file->setGroupName('group');
		$file->setOriginalName('test.txt');
		$file->setRelativePath('group/abc/test.txt');

		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::once())
			->method('findById')
			->with(7)
			->willReturn($file);
		$repository->expects(self::once())
			->method('remove')
			->with($file);

		$store = $this->createMock(StoreInterface::class);
		$store->method('getName')->willReturn('upload');
		$store->expects(self::once())
			->method('removeFile')
			->with($file);

		$storage = new Storage($repository, [$store], silentRemove: false);
		self::assertTrue($storage->removeById(7));
	}

	public function testRemoveCallsStoreFirstAndDoesNotTouchRepositoryWhenStoreFails(): void
	{
		$file = new File();
		$file->assignId(1);
		$file->setStoreName('upload');
		$file->setGroupName('group');
		$file->setOriginalName('test.txt');
		$file->setRelativePath('group/abc/test.txt');

		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::never())->method('remove');

		$store = $this->createMock(StoreInterface::class);
		$store->method('getName')->willReturn('upload');
		$store->expects(self::once())
			->method('removeFile')
			->with($file)
			->willThrowException(new RemoveException('store remove failed'));

		$storage = new Storage($repository, [$store], silentRemove: false);

		$this->expectException(RemoveException::class);
		$this->expectExceptionMessage('store remove failed');
		$storage->remove($file);
	}

	public function testRemoveSilentModeSwallowsStoreException(): void
	{
		$file = new File();
		$file->assignId(1);
		$file->setStoreName('upload');
		$file->setGroupName('group');
		$file->setOriginalName('test.txt');
		$file->setRelativePath('group/abc/test.txt');

		$repository = $this->createMock(RepositoryInterface::class);
		$repository->expects(self::never())->method('remove');

		$store = $this->createMock(StoreInterface::class);
		$store->method('getName')->willReturn('upload');
		$store->expects(self::once())
			->method('removeFile')
			->with($file)
			->willThrowException(new RemoveException('store remove failed'));

		$storage = new Storage($repository, [$store], silentRemove: true);
		$storage->remove($file);

		$this->addToAssertionCount(1);
	}
}
