<?php

declare(strict_types=1);

namespace Mheads\Yii\Filestorage;

use InvalidArgumentException;

final class StorageProvider
{
	public const string DEFAULT = 'default';

	/** @var array<string, StorageInterface> $storages */
	private static array $storages = [];

	/**
	 * Returns all connections.
	 *
	 * @return array<string, StorageInterface>
	 */
	public static function all(): array
	{
		return self::$storages;
	}

	public static function clear(): void
	{
		self::$storages = [];
	}

	public static function get(string $name = self::DEFAULT): StorageInterface
	{
		return self::$storages[$name]
			?? throw new InvalidArgumentException("Storage with name '$name' does not exist.");
	}

	public static function has(string $name = self::DEFAULT): bool
	{
		return isset(self::$storages[$name]);
	}

	public static function remove(string $name = self::DEFAULT): void
	{
		unset(self::$storages[$name]);
	}

	public static function set(StorageInterface $storage, string $name = self::DEFAULT): void
	{
		self::$storages[$name] = $storage;
	}
}
