<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store\FileSystem\Path;

interface PathGeneratorInterface
{
	public function generate(
		string $rootPath,
		string $groupDirName,
		string $fileName,
	): string;
}
