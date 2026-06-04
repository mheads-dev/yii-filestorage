<?php

declare(strict_types=1);

use App\Examples\Support\UploadedFileFactory;
use Mheads\Yii\Filestorage\Repository\DemoRepository;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageProvider;
use Mheads\Yii\Filestorage\Store\FileSystem\PrivateFileSystemStore;

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/support/UploadedFileFactory.php';

/**
 * Example:
 * - DemoRepository stores metadata in a local demo metadata file.
 * - PrivateFileSystemStore stores file in a directory without public URL.
 */

$runtimeRoot = __DIR__ . '/runtime';
if(!is_dir($runtimeRoot))
{
    mkdir($runtimeRoot, 0o777, true);
}

$repository = new DemoRepository($runtimeRoot . '/demo-files.json');

$privateRoot = __DIR__ . '/runtime/private-upload';
if(!is_dir($privateRoot))
{
    mkdir($privateRoot, 0o777, true);
}

$privateStore = new PrivateFileSystemStore(
    name: 'private',
    path: $privateRoot,
);

$storage = new Storage(
    repository: $repository,
    stores: [$privateStore],
    defaultStoreName: 'private',
);

// Required for File::getUrl()/getContent()/getResource() calls.
StorageProvider::set($storage);

$sourcePath = __DIR__ . '/runtime/source-private.txt';
file_put_contents($sourcePath, "Hello from private store example.\n");

$uploadedFile = UploadedFileFactory::fromLocalPath($sourcePath, 'demo-private.txt', 'text/plain');
$file = $storage->add(
    uploadedFile: $uploadedFile,
    groupName: 'internal',
    description: 'Private demo file',
);

printf("Saved id: %d\n", (int)$file->getId());
printf("Relative path: %s\n", (string)$file->getRelativePath());
printf("URL is null for private store: %s\n", var_export($file->getUrl(), true));

// Content can be read via facade.
printf("Loaded content: %s\n", (string)$file->getContent());

// Get file by id via facade.
$found = $storage->findById((int)$file->getId());
printf("Found by id: %s\n", $found?->getOriginalName() ?? 'not found');
