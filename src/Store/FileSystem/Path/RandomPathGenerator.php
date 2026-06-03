<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store\FileSystem\Path;

use Mheads\Yii\Filestorage\Helper;
use Override;
use Yiisoft\Files\FileHelper;

use function file_exists;

final readonly class RandomPathGenerator implements PathGeneratorInterface
{
	public function __construct(
		private int $segmentLength = 3,
		private int $maxAttempts = 1000,
	) {}

	#[Override]
	public function generate(
		string $rootPath,
		string $groupDirName,
		string $fileName,
	): string {
		$currentGroupPath = $groupDirName;
		$attempt = 0;

		do
		{
			$path = $currentGroupPath . '/' . Helper::randomDirName($this->segmentLength);
			++$attempt;

			if($attempt > $this->maxAttempts)
			{
				$currentGroupPath .= '/' . Helper::randomDirName($this->segmentLength);
				$attempt = 0;
			}
		}
		while(file_exists(
			FileHelper::normalizePath($rootPath . '/' . $path . '/' . $fileName),
		));

		return $path;
	}
}
