<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage;

use DateTimeImmutable;
use InvalidArgumentException;
use Mheads\Yii\Filestorage\Entity\FileInterface;
use Psr\Http\Message\UploadedFileInterface;
use Random\RandomException;
use Yiisoft\Strings\Inflector;

use function getimagesize;
use function is_string;
use function preg_replace;
use function random_int;
use function sprintf;
use function strlen;

final class Helper
{
	public static function sanitizeFileName(string $fileName): string
	{
		$fileName = preg_replace('/[\x00-\x1F\x7F]/u', '', $fileName) ?? '';

		$basename = pathinfo($fileName, PATHINFO_FILENAME);
		$extension = pathinfo($fileName, PATHINFO_EXTENSION);

		$fileName = (new Inflector())->toSlug($basename, '-', false);

		if($extension !== '')
		{
			$fileName .= '.' . $extension;
		}

		$fileName = preg_replace('/[^a-zA-Z0-9-_.\s]+/u', '', $fileName) ?? $fileName;
		return preg_replace('/\s/u', '_', $fileName) ?? $fileName;
	}

	/**
	 * @throws RandomException
	 */
	public static function randomDirName(int $length): string
	{
		$chars = 'qwertyuiopasdfghjklzxcvbnm1234567890';
		$n = strlen($chars) - 1;

		$result = '';

		for($i = 0; $i < $length; $i++)
		{
			$result .= $chars[random_int(0, $n)];
		}

		return $result;
	}

	/**
	 * @param class-string<FileInterface> $fileClass
	 */
	public static function createFileFromUploadedFile(
		UploadedFileInterface $uploadedFile,
		string                $groupName,
		string                $storeName,
		string                $fileClass,
		?string               $description = null,
	): FileInterface {
		if(!is_a($fileClass, FileInterface::class, true))
		{
			throw new InvalidArgumentException(
				sprintf(
					'File class must implement %s, %s given',
					FileInterface::class,
					$fileClass,
				),
			);
		}

		$file = new $fileClass();
		$file->setOriginalName($uploadedFile->getClientFilename() ?? 'unknown');
		$file->setFileSize($uploadedFile->getSize());
		$file->setContentType($uploadedFile->getClientMediaType());
		$file->setGroupName($groupName);
		$file->setStoreName($storeName);
		$file->setDescription($description);

		$uri = $uploadedFile->getStream()->getMetadata('uri');
		if(is_string($uri))
		{
			$imageInfo = @getimagesize($uri);
			if($imageInfo !== false)
			{
				[$width, $height] = $imageInfo;
				$file->setWidth($width);
				$file->setHeight($height);
			}
		}

		$now = new DateTimeImmutable();
		$file->setCreatedAt($now);
		$file->setUpdatedAt($now);

		return $file;
	}
}
