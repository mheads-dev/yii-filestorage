<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Store;

final readonly class AddFileResult
{
	public function __construct(
		public string  $relativePath,
		public ?string $externalId = null,
	) {}
}