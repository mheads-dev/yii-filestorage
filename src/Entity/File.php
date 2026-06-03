<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Entity;

use DateTimeImmutable;
use Mheads\Yii\Filestorage\StorageInterface;
use Mheads\Yii\Filestorage\StorageProvider;
use Override;

/**
 * @psalm-suppress ClassMustBeFinal
 * @psalm-suppress MissingConstructor
 */
class File implements FileInterface
{
	private int|string|null    $id = null;
	private string             $storeName;
	private ?string            $externalId = null;
	private string             $groupName;
	private ?string            $relativePath = null;
	private string             $originalName;
	private ?int               $height = null;
	private ?int               $width = null;
	private ?int               $fileSize = null;
	private ?string            $contentType = null;
	private ?string            $description = null;
	private ?DateTimeImmutable $updatedAt = null;
	private ?DateTimeImmutable $createdAt = null;

	#[Override]
	public static function storage(): StorageInterface
	{
		return StorageProvider::get();
	}

	#[Override]
	public function getId(): int|string|null
	{
		return $this->id;
	}

	#[Override]
	public function assignId(int|string $id): void
	{
		$this->id = $id;
	}

	#[Override]
	public function getStoreName(): string
	{
		return $this->storeName;
	}

	#[Override]
	public function setStoreName(string $value): void
	{
		$this->storeName = $value;
	}

	#[Override]
	public function getExternalId(): ?string
	{
		return $this->externalId;
	}

	#[Override]
	public function setExternalId(?string $value): void
	{
		$this->externalId = $value;
	}

	#[Override]
	public function getGroupName(): string
	{
		return $this->groupName;
	}

	#[Override]
	public function setGroupName(string $value): void
	{
		$this->groupName = $value;
	}

	#[Override]
	public function getRelativePath(): ?string
	{
		return $this->relativePath;
	}

	#[Override]
	public function setRelativePath(?string $value): void
	{
		$this->relativePath = $value;
	}

	#[Override]
	public function getOriginalName(): string
	{
		return $this->originalName;
	}

	#[Override]
	public function setOriginalName(string $value): void
	{
		$this->originalName = $value;
	}

	#[Override]
	public function getHeight(): ?int
	{
		return $this->height;
	}

	#[Override]
	public function setHeight(?int $value): void
	{
		$this->height = $value;
	}

	#[Override]
	public function getWidth(): ?int
	{
		return $this->width;
	}

	#[Override]
	public function setWidth(?int $value): void
	{
		$this->width = $value;
	}

	#[Override]
	public function getFileSize(): ?int
	{
		return $this->fileSize;
	}

	#[Override]
	public function setFileSize(?int $value): void
	{
		$this->fileSize = $value;
	}

	#[Override]
	public function getContentType(): ?string
	{
		return $this->contentType;
	}

	#[Override]
	public function setContentType(?string $value): void
	{
		$this->contentType = $value;
	}

	#[Override]
	public function getDescription(): ?string
	{
		return $this->description;
	}

	#[Override]
	public function setDescription(?string $value): void
	{
		$this->description = $value;
	}

	#[Override]
	public function getUpdatedAt(): ?DateTimeImmutable
	{
		return $this->updatedAt;
	}

	#[Override]
	public function setUpdatedAt(?DateTimeImmutable $value): void
	{
		$this->updatedAt = $value;
	}

	#[Override]
	public function getCreatedAt(): ?DateTimeImmutable
	{
		return $this->createdAt;
	}

	#[Override]
	public function setCreatedAt(?DateTimeImmutable $value): void
	{
		$this->createdAt = $value;
	}

	#[Override]
	public function getUrl(): ?string
	{
		return static::storage()->getUrl($this);
	}

	#[Override]
	public function getContent(): ?string
	{
		return static::storage()->getContent($this);
	}

	#[Override]
	public function getResource()
	{
		return static::storage()->getResource($this);
	}
}
