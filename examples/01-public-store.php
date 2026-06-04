<?php

declare(strict_types=1);

use App\Examples\Support\UploadedFileFactory;
use Mheads\Yii\Filestorage\Repository\DemoRepository;
use Mheads\Yii\Filestorage\Storage;
use Mheads\Yii\Filestorage\StorageProvider;
use Mheads\Yii\Filestorage\Store\FileSystem\PublicFileSystemStore;

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/support/UploadedFileFactory.php';

/**
 * Example:
 * - DemoRepository stores file metadata in a local demo metadata file.
 * - PublicFileSystemStore stores physical file in a public directory.
 */

$runtimeRoot = __DIR__ . '/runtime';
if(!is_dir($runtimeRoot))
{
    mkdir($runtimeRoot, 0o777, true);
}

$repository = new DemoRepository($runtimeRoot . '/demo-files.json');

$publicRoot = __DIR__ . '/runtime/public-upload';
if(!is_dir($publicRoot))
{
    mkdir($publicRoot, 0o777, true);
}

$publicStore = new PublicFileSystemStore(
    name: 'public',
    path: $publicRoot,
    baseUrl: 'https://cdn.example.com/uploads',
);

$storage = new Storage(
    repository: $repository,
    stores: [$publicStore],
    defaultStoreName: 'public',
);

// Required for File::getUrl()/getContent()/getResource() calls.
StorageProvider::set($storage);

// Create local source file for demo.
$sourcePath = __DIR__ . '/runtime/source-public.txt';
file_put_contents($sourcePath, "Hello from public store example.\n");

$uploadedFile = UploadedFileFactory::fromLocalPath($sourcePath, 'demo-public.txt', 'text/plain');
$file = $storage->add(
    uploadedFile: $uploadedFile,
    groupName: 'docs',
    description: 'Public demo file',
);

printf("Saved id: %d\n", (int)$file->getId());
printf("Relative path: %s\n", (string)$file->getRelativePath());
printf("Public URL: %s\n", (string)$file->getUrl());

// Get file by id via facade.
$found = $storage->findById((int)$file->getId());
printf("Found by id: %s\n", $found?->getOriginalName() ?? 'not found');
