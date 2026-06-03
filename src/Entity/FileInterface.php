<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage\Entity;

use DateTimeImmutable;
use Mheads\Yii\Filestorage\Exception\InvalidConfigException;
use Mheads\Yii\Filestorage\StorageInterface;

interface FileInterface
{
	public function getId(): int|string|null;

	public function assignId(int|string $id): void;

	public function getStoreName(): string;

	public function setStoreName(string $value): void;

	public function getExternalId(): ?string;

	public function setExternalId(?string $value): void;

	public function getGroupName(): string;

	public function setGroupName(string $value): void;

	public function getRelativePath(): ?string;

	public function setRelativePath(?string $value): void;

	public function getOriginalName(): string;

	public function setOriginalName(string $value): void;

	public function getHeight(): ?int;

	public function setHeight(?int $value): void;

	public function getWidth(): ?int;

	public function setWidth(?int $value): void;

	public function getFileSize(): ?int;

	public function setFileSize(?int $value): void;

	public function getContentType(): ?string;

	public function setContentType(?string $value): void;

	public function getDescription(): ?string;

	public function setDescription(?string $value): void;

	public function getUpdatedAt(): ?DateTimeImmutable;

	public function setUpdatedAt(?DateTimeImmutable $value): void;

	public function getCreatedAt(): ?DateTimeImmutable;

	public function setCreatedAt(?DateTimeImmutable $value): void;

	public static function storage(): StorageInterface;

	/**
	 * Get file URL (for public files)
	 * @throws InvalidConfigException
	 */
	public function getUrl(): ?string;

	/**
	 * Get file content
	 * @throws InvalidConfigException
	 */
	public function getContent(): ?string;

	/**
	 * Get file resource
	 * @return resource|null
	 * @throws InvalidConfigException
	 */
	public function getResource();
}
